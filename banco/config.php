<?php
// Elite Sistema - Configuração do Banco de Dados
// ==============================================

$host = 'localhost';
$usuario_bd = 'root';
$senha_bd = '';
$banco = 'elite_sistema';
$porta = 3306;

try {
    $conexao = new mysqli($host, $usuario_bd, $senha_bd, $banco, $porta);
    
    if ($conexao->connect_error) {
        die("Erro de conexão: " . $conexao->connect_error);
    }
    
    // Configurar charset
    $conexao->set_charset("utf8mb4");

    // Garantir que a tabela de fiados exista no banco de dados
    criar_tabela_fiados_se_nao_existe();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}

function criar_tabela_fiados_se_nao_existe() {
    global $conexao;

    $query = "CREATE TABLE IF NOT EXISTS fiados (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(150) NOT NULL,
        telefone VARCHAR(30),
        valor DECIMAL(12,2) NOT NULL,
        data_vencimento DATE DEFAULT NULL,
        descricao TEXT,
        interagiu_whatsapp BOOLEAN DEFAULT FALSE,
        status ENUM('pendente','pago','atrasado') DEFAULT 'pendente',
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $conexao->query($query);
    $conexao->query("ALTER TABLE fiados MODIFY status ENUM('pendente','pago','atrasado') DEFAULT 'pendente'");
    $conexao->query("ALTER TABLE fiados ADD COLUMN IF NOT EXISTS interagiu_whatsapp BOOLEAN DEFAULT FALSE");
}

// Constantes da aplicação
define('SITE_URL', 'http://localhost/elite_sistema/');
define('NOME_APP', 'Elite Sistema - Loja de Roupas');
define('TEMPO_SESSAO', 3600); // 1 hora

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função auxiliar para debug
function debug($dados, $parar = false) {
    echo '<pre>';
    var_dump($dados);
    echo '</pre>';
    if ($parar) die;
}

// Função para escapar strings
function escapar($string) {
    global $conexao;
    return $conexao->real_escape_string($string);
}

// Verifica se a requisição é para uma API
function is_api_request() {
    return isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false
        || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
}

// Função para verificar login
function verificar_login() {
    if (!isset($_SESSION['usuario_id'])) {
        if (is_api_request()) {
            http_response_code(401);
            json_response('erro', 'Sessão expirada ou não autenticado');
        }

        header('Location: ' . SITE_URL . 'login.php');
        exit;
    }
}

// Função para fazer logout
function fazer_logout() {
    session_destroy();
    header('Location: ' . SITE_URL . 'login.php');
    exit;
}

// Função para retornar JSON
function json_response($status, $mensagem, $dados = null) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => $status,
        'mensagem' => $mensagem,
        'dados' => $dados
    ]);
    exit;
}

// Processa logout quando acessado diretamente via query string
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    fazer_logout();
}
?>
