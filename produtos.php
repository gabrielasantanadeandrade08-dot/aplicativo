<?php
require_once 'banco/config.php';
verificar_login();

$usuario_nome = $_SESSION['usuario_nome'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Elite Sistema</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-brand">🏪 Elite Sistema</div>
        <div class="navbar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="produtos.php">Produtos</a>
            <a href="devedores.php">Fiados</a>
            <a href="painel-leitor.php">Leitor de Código</a>
            <span><?php echo $usuario_nome; ?></span>
            <a href="banco/config.php?logout=1" class="btn-sair">Sair</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">📦 Gerenciamento de Produtos</h1>
                <div style="display: flex; gap: 1rem;">
                    <a href="novo-produto.php" class="btn btn-primary">+ Novo Produto</a>
                    <a href="api/exportar.php?tipo=produtos" class="btn btn-secondary">📥 Exportar para Excel</a>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Código de Barras</th>
                        <th>Nome</th>
                        <th>Cadastrado por</th>
                        <th>Valor</th>
                        <th>Estoque</th>
                        <th>Tamanho</th>
                        <th>Cor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-produtos">
                    <tr>
                        <td colspan="8" style="text-align: center;">
                            <span class="loading"></span> Carregando...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script>
        window.addEventListener('load', function() {
            listarProdutos();
        });
    </script>
</body>
</html>
