<?php
// Limpar banco de dados - remover dados iniciais
require_once 'config.php';

echo "🗑️ Limpando banco de dados...<br><br>";

// Deletar em ordem para respeitar chaves estrangeiras
$queries = [
    "DELETE FROM leitura_barras_log;",
    "DELETE FROM vendas_itens;",
    "DELETE FROM vendas;",
    "DELETE FROM produtos;",
    "DELETE FROM usuarios;",
];

$total_deletados = 0;

foreach ($queries as $query) {
    if ($conexao->query($query)) {
        $total_deletados += $conexao->affected_rows;
    } else {
        echo "❌ Erro ao executar query: " . $conexao->error . "<br>";
    }
}

// Resetar auto_increment
$reset_queries = [
    "ALTER TABLE usuarios AUTO_INCREMENT = 1;",
    "ALTER TABLE produtos AUTO_INCREMENT = 1;",
    "ALTER TABLE vendas AUTO_INCREMENT = 1;",
    "ALTER TABLE vendas_itens AUTO_INCREMENT = 1;",
    "ALTER TABLE leitura_barras_log AUTO_INCREMENT = 1;",
];

foreach ($reset_queries as $query) {
    $conexao->query($query);
}

echo "✅ Banco de dados limpo com sucesso!<br>";
echo "📊 Registros deletados: $total_deletados<br><br>";
echo "<strong>Seu banco está vazio e pronto para cadastrar seus próprios dados!</strong><br><br>";
echo "<a href='../index.php' style='padding: 10px 20px; background: #1e40af; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>← Voltar ao sistema</a>";

$conexao->close();
?>
