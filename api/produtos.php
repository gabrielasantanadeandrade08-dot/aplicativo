<?php
// API - Produtos
// ==============

require_once '../banco/config.php';

verificar_login();

$acao = isset($_POST['acao']) ? $_POST['acao'] : (isset($_GET['acao']) ? $_GET['acao'] : '');

switch ($acao) {
    
    case 'listar':
        listar_produtos();
        break;
        
    case 'buscar_por_barras':
        buscar_por_barras();
        break;
        
    case 'criar':
        criar_produto();
        break;
        
    case 'editar':
        editar_produto();
        break;
        
    case 'deletar':
        deletar_produto();
        break;
        
    case 'obter':
        obter_produto();
        break;
        
    default:
        json_response('erro', 'Ação não especificada');
}

function listar_produtos() {
    global $conexao;
    
    $query = "SELECT p.*, u.nome as usuario_nome FROM produtos p 
              LEFT JOIN usuarios u ON p.usuario_id = u.id 
              WHERE p.status = 'ativo' 
              ORDER BY p.data_criacao DESC";
    
    $resultado = $conexao->query($query);
    $produtos = [];
    
    while ($produto = $resultado->fetch_assoc()) {
        $produtos[] = $produto;
    }
    
    json_response('sucesso', 'Produtos listados', $produtos);
}

function buscar_por_barras() {
    global $conexao;
    
    $codigo_barras = isset($_POST['codigo_barras']) ? escapar($_POST['codigo_barras']) : '';
    
    if (empty($codigo_barras)) {
        json_response('erro', 'Código de barras não informado');
    }
    
    $query = "SELECT p.*, u.nome as usuario_nome FROM produtos p 
              LEFT JOIN usuarios u ON p.usuario_id = u.id 
              WHERE p.codigo_barras = '$codigo_barras' AND p.status = 'ativo'";
    
    $resultado = $conexao->query($query);
    
    if ($resultado->num_rows == 0) {
        // Registrar tentativa de leitura falha
        $usuario_id = $_SESSION['usuario_id'];
        $conexao->query("INSERT INTO leitura_barras_log (usuario_id, codigo_barras, sucesso) 
                        VALUES ($usuario_id, '$codigo_barras', FALSE)");
        json_response('erro', 'Produto não encontrado para este código de barras');
    }
    
    $produto = $resultado->fetch_assoc();
    
    // Registrar leitura bem-sucedida
    $usuario_id = $_SESSION['usuario_id'];
    $produto_id = $produto['id'];
    $conexao->query("INSERT INTO leitura_barras_log (usuario_id, codigo_barras, produto_id, sucesso) 
                    VALUES ($usuario_id, '$codigo_barras', $produto_id, TRUE)");
    
    json_response('sucesso', 'Produto encontrado', $produto);
}

function criar_produto() {
    global $conexao;
    
    $codigo_barras = isset($_POST['codigo_barras']) ? escapar($_POST['codigo_barras']) : '';
    $nome = isset($_POST['nome']) ? escapar($_POST['nome']) : '';
    $descricao = isset($_POST['descricao']) ? escapar($_POST['descricao']) : '';
    $usuario_id = $_SESSION['usuario_id'];
    $preco = isset($_POST['preco']) ? (float)$_POST['preco'] : 0;
    $estoque = isset($_POST['estoque']) ? (int)$_POST['estoque'] : 0;
    $tamanho = isset($_POST['tamanho']) ? escapar($_POST['tamanho']) : '';
    $cor = isset($_POST['cor']) ? escapar($_POST['cor']) : '';
    $marca = isset($_POST['marca']) ? escapar($_POST['marca']) : '';
    
    // Validações
    if (empty($codigo_barras) || empty($nome) || $preco <= 0) {
        json_response('erro', 'Código de barras, nome e preço são obrigatórios');
    }
    
    // Verificar se código já existe
    $query_check = "SELECT id FROM produtos WHERE codigo_barras = '$codigo_barras'";
    $resultado_check = $conexao->query($query_check);
    
    if ($resultado_check->num_rows > 0) {
        json_response('erro', 'Este código de barras já existe');
    }
    
    $query = "INSERT INTO produtos (codigo_barras, nome, descricao, usuario_id, preco, estoque, tamanho, cor, marca) 
              VALUES ('$codigo_barras', '$nome', '$descricao', $usuario_id, $preco, $estoque, '$tamanho', '$cor', '$marca')";
    
    if ($conexao->query($query)) {
        $novo_id = $conexao->insert_id;

        // Notificação por e-mail
        $destinatario = 'messiasmdesa463@gmail.com'; // E-mail definido pelo usuário
        $assunto = 'Novo produto cadastrado';
        $mensagem = "Um novo produto foi cadastrado:\n\n" .
            "Nome: $nome\n" .
            "Código de Barras: $codigo_barras\n" .
            "Preço: R$ $preco\n" .
            "Estoque: $estoque\n" .
            "Tamanho: $tamanho\n" .
            "Cor: $cor\n" .
            "Marca: $marca\n" .
            "Descrição: $descricao\n";
        $headers = "From: notifica@elite.com\r\n" .
                   "Content-Type: text/plain; charset=utf-8";
        @mail($destinatario, $assunto, $mensagem, $headers);

        // Exemplo de integração WhatsApp (Z-API, UltraMsg, Twilio, etc)
        // $whatsapp_api_url = 'https://api.z-api.io/instances/SEU_INSTANCE/token/SEU_TOKEN/send-text';
        // $payload = [
        //     'phone' => 'SEU_NUMERO',
        //     'message' => "Novo produto cadastrado: $nome, código: $codigo_barras, preço: R$ $preco"
        // ];
        // $ch = curl_init($whatsapp_api_url);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $result = curl_exec($ch);
        // curl_close($ch);

        json_response('sucesso', 'Produto criado com sucesso', ['id' => $novo_id]);
    } else {
        json_response('erro', 'Erro ao criar produto: ' . $conexao->error);
    }
}

function editar_produto() {
    global $conexao;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nome = isset($_POST['nome']) ? escapar($_POST['nome']) : '';
    $descricao = isset($_POST['descricao']) ? escapar($_POST['descricao']) : '';
    $preco = isset($_POST['preco']) ? (float)$_POST['preco'] : 0;
    $estoque = isset($_POST['estoque']) ? (int)$_POST['estoque'] : 0;
    $tamanho = isset($_POST['tamanho']) ? escapar($_POST['tamanho']) : '';
    $cor = isset($_POST['cor']) ? escapar($_POST['cor']) : '';
    $marca = isset($_POST['marca']) ? escapar($_POST['marca']) : '';
    
    if ($id <= 0) {
        json_response('erro', 'ID do produto inválido');
    }
    
    $query = "UPDATE produtos SET nome='$nome', descricao='$descricao', 
              preco=$preco, estoque=$estoque, tamanho='$tamanho', cor='$cor', marca='$marca' 
              WHERE id = $id";
    
    if ($conexao->query($query)) {
        json_response('sucesso', 'Produto atualizado com sucesso');
    } else {
        json_response('erro', 'Erro ao atualizar produto: ' . $conexao->error);
    }
}

function deletar_produto() {
    global $conexao;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id <= 0) {
        json_response('erro', 'ID do produto inválido');
    }
    
    $query = "UPDATE produtos SET status = 'inativo' WHERE id = $id";
    
    if ($conexao->query($query)) {
        json_response('sucesso', 'Produto deletado com sucesso');
    } else {
        json_response('erro', 'Erro ao deletar produto: ' . $conexao->error);
    }
}

function obter_produto() {
    global $conexao;
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id <= 0) {
        json_response('erro', 'ID do produto inválido');
    }
    
    $query = "SELECT * FROM produtos WHERE id = $id";
    $resultado = $conexao->query($query);
    
    if ($resultado->num_rows == 0) {
        json_response('erro', 'Produto não encontrado');
    }
    
    $produto = $resultado->fetch_assoc();
    json_response('sucesso', 'Produto obtido', $produto);
}
?>
