<?php
// Elite Sistema - Setup Automático
// ==================================

$host = 'localhost';
$usuario_bd = 'root';
$senha_bd = '';

// Tentar conectar ao MySQL sem banco específico
$conexao_temp = @new mysqli($host, $usuario_bd, $senha_bd);

if ($conexao_temp->connect_error) {
    die("❌ Erro ao conectar ao MySQL. Certifique-se de que o MySQL está rodando.<br>
         Erro: " . $conexao_temp->connect_error);
}

// Criar banco de dados
$sql_criar_banco = "CREATE DATABASE IF NOT EXISTS elite_sistema;";
if (!$conexao_temp->query($sql_criar_banco)) {
    die("❌ Erro ao criar banco de dados: " . $conexao_temp->error);
}

// Conectar ao banco elite_sistema
$conexao = new mysqli($host, $usuario_bd, $senha_bd, 'elite_sistema');

if ($conexao->connect_error) {
    die("❌ Erro ao conectar ao banco: " . $conexao->connect_error);
}

$conexao->set_charset("utf8mb4");

// Ler e executar o schema.sql
$schema_path = __DIR__ . '/schema.sql';
if (!file_exists($schema_path)) {
    die("❌ Arquivo schema.sql não encontrado em: " . $schema_path);
}

$schema = file_get_contents($schema_path);

// Dividir em múltiplas queries (separadas por ;)
$queries = array_filter(array_map('trim', explode(';', $schema)));

$erros = [];
foreach ($queries as $query) {
    if (!empty($query)) {
        if (!$conexao->query($query)) {
            $erros[] = $conexao->error;
        }
    }
}

if (empty($erros)) {
    echo "✅ Sistema configurado com sucesso!<br><br>";
    echo "<strong>✅ Banco de dados criado</strong><br>";
    echo "<strong>✅ Tabelas criadas</strong><br>";
    echo "<strong>✅ Dados iniciais inseridos</strong><br><br>";
    
    echo "<h3>🔑 Credenciais de teste:</h3>";
    echo "Email: <strong>admin@elite.com</strong><br>";
    echo "Senha: <strong>123456</strong><br><br>";
    
    echo "<a href='../index.php' style='padding: 10px 20px; background: #1e40af; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>→ Ir para página inicial</a>";
} else {
    echo "⚠️ Erro ao executar o schema:<br>";
    foreach ($erros as $erro) {
        echo "- " . htmlspecialchars($erro) . "<br>";
    }
}

$conexao->close();
$conexao_temp->close();
?>
