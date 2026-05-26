<?php
// API - Autenticação
// ==================

require_once '../banco/config.php';

$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

switch ($acao) {
    case 'editar_usuario':
        editar_usuario();
        break;
    case 'excluir_usuario':
        excluir_usuario();
        break;
    
    case 'login':
        login();
        break;
        
    case 'registrar':
        registrar();
        break;
        
    case 'logout':
        fazer_logout();
        break;
        
    default:
        json_response('erro', 'Ação não especificada');
}

function login() {
    global $conexao;
    
    $email = isset($_POST['email']) ? escapar($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    
    if (empty($email) || empty($senha)) {
        json_response('erro', 'Email e senha são obrigatórios');
    }
    
    $query = "SELECT id, nome, email, senha FROM usuarios WHERE email = '$email' AND status = 'ativo'";
    $resultado = $conexao->query($query);
    
    if ($resultado->num_rows == 0) {
        json_response('erro', 'Usuário ou senha inválidos');
    }
    
    $usuario = $resultado->fetch_assoc();
    
    // Verificar senha com bcrypt
    if (!password_verify($senha, $usuario['senha'])) {
        json_response('erro', 'Usuário ou senha inválidos');
    }
    
    // Verificar fiados vencidos há mais de 30 dias
    $usuario_id = (int)$usuario['id'];
    $query_fiados = "SELECT COUNT(*) as total FROM fiados WHERE usuario_id = $usuario_id AND status = 'pendente' AND data_vencimento IS NOT NULL AND data_vencimento <= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
    $resultado_fiados = $conexao->query($query_fiados);
    $bloqueado = false;
    if ($resultado_fiados) {
        $row_fiado = $resultado_fiados->fetch_assoc();
        if ($row_fiado['total'] > 0) {
            $bloqueado = true;
        }
    }

    if ($bloqueado) {
        json_response('erro', 'Acesso bloqueado: você possui fiados vencidos há mais de 30 dias. Regularize sua situação para acessar o sistema.');
    }

    // Criar sessão normalmente
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_login_time'] = time();

    json_response('sucesso', 'Login realizado com sucesso', [
        'usuario_id' => $usuario['id'],
        'usuario_nome' => $usuario['nome']
    ]);
}

function registrar() {
    global $conexao;
    
    $nome = isset($_POST['nome']) ? escapar($_POST['nome']) : '';
    $email = isset($_POST['email']) ? escapar($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $confirmar_senha = isset($_POST['confirmar_senha']) ? $_POST['confirmar_senha'] : '';
    
    // Validações
    if (empty($nome) || empty($email) || empty($senha)) {
        json_response('erro', 'Todos os campos são obrigatórios');
    }
    
    if (strlen($nome) < 3) {
        json_response('erro', 'Nome deve ter no mínimo 3 caracteres');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response('erro', 'Email inválido');
    }
    
    if (strlen($senha) < 6) {
        json_response('erro', 'Senha deve ter no mínimo 6 caracteres');
    }
    
    if ($senha !== $confirmar_senha) {
        json_response('erro', 'Senhas não conferem');
    }
    
    // Verificar se email já existe
    $query_check = "SELECT id FROM usuarios WHERE email = '$email'";
    $resultado_check = $conexao->query($query_check);
    
    if ($resultado_check->num_rows > 0) {
        json_response('erro', 'Este email já está cadastrado');
    }
    
    // Criptografar senha
    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
    
    // Inserir usuário
    $query = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha_hash')";
    
    if ($conexao->query($query)) {
        json_response('sucesso', 'Usuário cadastrado com sucesso! Faça login para continuar');
    } else {
        json_response('erro', 'Erro ao cadastrar usuário: ' . $conexao->error);
    }
}
?>
// Editar dados do usuário logado (nome, email, telefone, senha)
function editar_usuario() {
    global $conexao;
    if (!isset($_SESSION['usuario_id'])) {
        json_response('erro', 'Não autenticado');
    }
    $usuario_id = (int)$_SESSION['usuario_id'];
    $campos = [];
    if (isset($_POST['nome']) && strlen(trim($_POST['nome'])) > 2) {
        $campos[] = "nome='" . escapar($_POST['nome']) . "'";
        $_SESSION['usuario_nome'] = $_POST['nome'];
    }
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email = escapar($_POST['email']);
        $q = "SELECT id FROM usuarios WHERE email='$email' AND id<>$usuario_id";
        $r = $conexao->query($q);
        if ($r && $r->num_rows > 0) {
            json_response('erro', 'Email já cadastrado para outro usuário');
        }
        $campos[] = "email='$email'";
        $_SESSION['usuario_email'] = $_POST['email'];
    }
    if (isset($_POST['telefone'])) {
        $tel = escapar($_POST['telefone']);
        $campos[] = "telefone='$tel'";
    }
    if (isset($_POST['endereco'])) {
        $end = escapar($_POST['endereco']);
        $campos[] = "endereco='$end'";
    }
    if (isset($_POST['data_nascimento']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['data_nascimento'])) {
        $data_nasc = escapar($_POST['data_nascimento']);
        $campos[] = "data_nascimento='$data_nasc'";
    }
    // Upload de foto de perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $dest = '../uploads/perfil_' . $usuario_id . '_' . time() . '.' . $ext;
            if (!is_dir('../uploads')) mkdir('../uploads', 0777, true);
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $dest)) {
                $dest_rel = 'uploads/' . basename($dest);
                $campos[] = "foto_perfil='" . escapar($dest_rel) . "'";
            }
        }
    }
    if (isset($_POST['senha']) && strlen($_POST['senha']) >= 6) {
        $senha_hash = password_hash($_POST['senha'], PASSWORD_BCRYPT);
        $campos[] = "senha='$senha_hash'";
    }
    if (empty($campos)) {
        json_response('erro', 'Nenhum dado válido para atualizar');
    }
    $sql = "UPDATE usuarios SET " . implode(',', $campos) . " WHERE id=$usuario_id";
    if ($conexao->query($sql)) {
        json_response('sucesso', 'Dados atualizados com sucesso');
    } else {
        json_response('erro', 'Erro ao atualizar: ' . $conexao->error);
    }
}

// Excluir conta do usuário logado
function excluir_usuario() {
    global $conexao;
    if (!isset($_SESSION['usuario_id'])) {
        json_response('erro', 'Não autenticado');
    }
    $usuario_id = (int)$_SESSION['usuario_id'];
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    if (strlen($senha) < 6) {
        json_response('erro', 'Senha obrigatória para excluir a conta');
    }
    $q = "SELECT senha FROM usuarios WHERE id=$usuario_id";
    $r = $conexao->query($q);
    if (!$r || $r->num_rows == 0) {
        json_response('erro', 'Usuário não encontrado');
    }
    $row = $r->fetch_assoc();
    if (!password_verify($senha, $row['senha'])) {
        json_response('erro', 'Senha incorreta');
    }
    $sql = "DELETE FROM usuarios WHERE id=$usuario_id";
    if ($conexao->query($sql)) {
        session_destroy();
        json_response('sucesso', 'Conta excluída com sucesso');
    } else {
        json_response('erro', 'Erro ao excluir conta: ' . $conexao->error);
    }
}
