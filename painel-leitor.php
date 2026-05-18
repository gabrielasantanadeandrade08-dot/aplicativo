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
    <title>Leitor de Código de Barras - Elite Sistema</title>
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
        <!-- PAINEL LEITOR -->
        <div class="leitor-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <h1 style="color: var(--cor-primaria);">🔍 Leitor de Código de Barras</h1>
                <a href="api/exportar.php?tipo=leituras" class="btn btn-secondary">📥 Exportar Leituras</a>
                <div style="font-size: 0.9rem; color: #64748b;">
                    <strong>Usuário:</strong> <?php echo $usuario_nome; ?>
                </div>
            </div>
            
            <p style="color: #64748b; margin-bottom: 1.5rem;">
                Digite ou escaneie um código de barras para buscar o produto automaticamente.
            </p>
            
            <div class="leitor-input-group">
                <input 
                    type="text" 
                    id="codigo-barras" 
                    placeholder="📱 Cole o código de barras aqui..." 
                    autofocus
                >
                <button type="button" onclick="lerCodigoBarras()" class="btn btn-primary">
                    Buscar
                </button>
            </div>
            
            <!-- RESULTADO DA LEITURA -->
            <div id="resultado-leitor"></div>
        </div>

        <!-- PRODUTOS RECENTES -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📦 Produtos Cadastrados</h2>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Código de Barras</th>
                        <th>Produto</th>
                        <th>Valor</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-produtos">
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <span class="loading"></span> Carregando...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- HISTÓRICO DE LEITURAS -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📊 Histórico de Leituras</h2>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usuario_id = $_SESSION['usuario_id'];
                    $query = "SELECT l.*, p.nome as produto_nome FROM leitura_barras_log l 
                              LEFT JOIN produtos p ON l.produto_id = p.id 
                              WHERE l.usuario_id = $usuario_id 
                              ORDER BY l.data_leitura DESC 
                              LIMIT 15";
                    $resultado = $conexao->query($query);
                    
                    if ($resultado->num_rows > 0) {
                        while ($leitura = $resultado->fetch_assoc()) {
                            $status = $leitura['sucesso'] ? '✅ Encontrado' : '❌ Não encontrado';
                            $data = date('d/m/Y H:i:s', strtotime($leitura['data_leitura']));
                            $produto_nome = isset($leitura['produto_nome']) ? $leitura['produto_nome'] : 'Produto não encontrado';
                            echo "<tr>
                                    <td>{$data}</td>
                                    <td><strong>{$leitura['codigo_barras']}</strong></td>
                                    <td>{$produto_nome}</td>
                                    <td>{$status}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center; color: #64748b;'>Nenhuma leitura realizada ainda</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script>
        // Carrega lista de produtos ao abrir a página
        window.addEventListener('load', function() {
            listarProdutos();
        });
    </script>
</body>
</html>
