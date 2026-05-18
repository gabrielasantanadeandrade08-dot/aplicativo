<?php
// API - Autenticação
// ==================

require_once '../banco/config.php';

$acao = isset($_POST['acao']) ? $_POST['acao'] : '';

switch ($acao) {
    
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
    
    // Criar sessão
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
