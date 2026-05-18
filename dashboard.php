<?php
require_once 'banco/config.php';
verificar_login();

// Obter estatísticas
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Total de produtos
$query_produtos = "SELECT COUNT(*) as total FROM produtos WHERE status = 'ativo'";
$resultado_produtos = $conexao->query($query_produtos);
$total_produtos = $resultado_produtos->fetch_assoc()['total'];

// Total de vendas
$query_vendas = "SELECT COUNT(*) as total, SUM(total) as valor FROM vendas WHERE usuario_id = $usuario_id";
$resultado_vendas = $conexao->query($query_vendas);
$dados_vendas = $resultado_vendas->fetch_assoc();
$total_vendas = $dados_vendas['total'];
$valor_vendas = $dados_vendas['valor'] ?? 0;

// Estoque total
$query_estoque = "SELECT SUM(estoque) as total FROM produtos WHERE status = 'ativo'";
$resultado_estoque = $conexao->query($query_estoque);
$total_estoque = $resultado_estoque->fetch_assoc()['total'] ?? 0;

// Leituras recentes
$query_leituras = "SELECT COUNT(*) as total FROM leitura_barras_log WHERE usuario_id = $usuario_id AND data_leitura >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$resultado_leituras = $conexao->query($query_leituras);
$total_leituras = $resultado_leituras->fetch_assoc()['total'];

// Total de usuários cadastrados
$query_usuarios = "SELECT COUNT(*) as total FROM usuarios";
$resultado_usuarios = $conexao->query($query_usuarios);
$total_usuarios = $resultado_usuarios->fetch_assoc()['total'];

// Últimos usuários cadastrados
$query_usuarios_recentes = "SELECT id, nome, email, data_criacao, status FROM usuarios ORDER BY data_criacao DESC LIMIT 5";
$resultado_usuarios_recentes = $conexao->query($query_usuarios_recentes);

// Total de fiados pendentes
$query_fiados = "SELECT COUNT(*) as total, SUM(valor) as valor_total FROM fiados WHERE status = 'pendente'";
$resultado_fiados = $conexao->query($query_fiados);
$dados_fiados = $resultado_fiados->fetch_assoc();
$total_devedores = $dados_fiados['total'];
$valor_devedores = $dados_fiados['valor_total'] ?? 0;

// Fiados vencendo hoje
$query_vencendo = "SELECT COUNT(*) as total FROM fiados WHERE status = 'pendente' AND data_vencimento = CURDATE()";
$resultado_vencendo = $conexao->query($query_vencendo);
$total_vencendo = $resultado_vencendo->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Elite Sistema</title>
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
            <a href="atualizacao.php">Atualização</a>
            <span><?php echo $usuario_nome; ?></span>
            <a href="banco/config.php?logout=1" class="btn-sair">Sair</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">📊 Dashboard</h1>
                <div style="display: flex; gap: 1rem;">
                    <a href="api/exportar.php?tipo=produtos" class="btn btn-secondary" title="Exportar Produtos">📥 Produtos</a>
                    <a href="api/exportar.php?tipo=vendas" class="btn btn-secondary" title="Exportar Vendas">📥 Vendas</a>
                    <a href="api/exportar.php?tipo=leituras" class="btn btn-secondary" title="Exportar Leituras">📥 Leituras</a>
                    <a href="api/exportar.php?tipo=fiados" class="btn btn-secondary" title="Exportar Fiados">📥 Fiados</a>
                </div>
            </div>
            
            <p style="margin-bottom: 2rem; color: #64748b;">
                Bem-vindo, <strong><?php echo $usuario_nome; ?></strong>! 👋
            </p>
            
            <!-- CARDS DE ESTATÍSTICAS -->
            <div class="dashboard-grid">
                <div class="stat-card primaria">
                    <div class="stat-label">📦 Total de Produtos</div>
                    <div class="stat-valor"><?php echo $total_produtos; ?></div>
                </div>
                
                <div class="stat-card sucesso">
                    <div class="stat-label">💰 Total de Vendas</div>
                    <div class="stat-valor"><?php echo $total_vendas; ?></div>
                </div>
                
                <div class="stat-card aviso">
                    <div class="stat-label">📊 Valor em Vendas</div>
                    <div class="stat-valor">R$ <?php echo number_format($valor_vendas, 2, ',', '.'); ?></div>
                </div>
                
                <div class="stat-card primaria">
                    <div class="stat-label">📈 Unidades em Estoque</div>
                    <div class="stat-valor"><?php echo $total_estoque; ?></div>
                </div>
                
                <div class="stat-card sucesso">
                    <div class="stat-label">🔍 Leituras (7 dias)</div>
                    <div class="stat-valor"><?php echo $total_leituras; ?></div>
                </div>

                <div class="stat-card aviso">
                    <div class="stat-label">👥 Usuários Cadastrados</div>
                    <div class="stat-valor"><?php echo $total_usuarios; ?></div>
                </div>

                <div class="stat-card aviso">
                    <div class="stat-label">🧾 Fiados Pendentes</div>
                    <div class="stat-valor"><?php echo $total_devedores; ?></div>
                </div>

                <div class="stat-card aviso">
                    <div class="stat-label">💸 Total em Fiados</div>
                    <div class="stat-valor">R$ <?php echo number_format($valor_devedores, 2, ',', '.'); ?></div>
                </div>

                <div class="stat-card erro">
                    <div class="stat-label">⏰ Fiados vencendo hoje</div>
                    <div class="stat-valor"><?php echo $total_vencendo; ?></div>
                </div>
            </div>
        </div>

        <!-- AÇÕES RÁPIDAS -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">⚡ Ações Rápidas</h2>
            </div>
            
            <div class="grid">
                <div class="grid-item">
                    <h3 style="color: var(--cor-primaria); margin-bottom: 1rem;">➕ Novo Produto</h3>
                    <p style="color: #64748b; margin-bottom: 1rem;">Cadastre um novo produto no seu sistema</p>
                    <a href="novo-produto.php" class="btn btn-primary">Adicionar</a>
                </div>
                
                <div class="grid-item">
                    <h3 style="color: var(--cor-primaria); margin-bottom: 1rem;">🔍 Leitor de Códigos</h3>
                    <p style="color: #64748b; margin-bottom: 1rem;">Leia códigos de barras rapidamente</p>
                    <a href="painel-leitor.php" class="btn btn-secondary">Acessar</a>
                </div>
                
                <div class="grid-item">
                    <h3 style="color: var(--cor-primaria); margin-bottom: 1rem;">📋 Listar Produtos</h3>
                    <p style="color: #64748b; margin-bottom: 1rem;">Veja todos os produtos cadastrados</p>
                    <a href="produtos.php" class="btn btn-primary">Ver Lista</a>
                </div>

            </div>
        </div>

        <!-- ÚLTIMAS LEITURAS -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📜 Últimas Leituras de Código</h2>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Código de Barras</th>
                        <th>Produto</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT l.*, p.nome as produto_nome FROM leitura_barras_log l 
                              LEFT JOIN produtos p ON l.produto_id = p.id 
                              WHERE l.usuario_id = $usuario_id 
                              ORDER BY l.data_leitura DESC 
                              LIMIT 10";
                    $resultado = $conexao->query($query);
                    
                    if ($resultado->num_rows > 0) {
                        while ($leitura = $resultado->fetch_assoc()) {
                            $status = $leitura['sucesso'] ? '✅ Sucesso' : '❌ Falha';
                            $data = date('d/m/Y H:i', strtotime($leitura['data_leitura']));
                            $produto_nome = isset($leitura['produto_nome']) ? $leitura['produto_nome'] : 'N/A';
                            echo "<tr>
                                    <td>{$data}</td>
                                    <td>{$leitura['codigo_barras']}</td>
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

        <!-- ÚLTIMOS USUÁRIOS CADASTRADOS -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">👥 Últimos Usuários Cadastrados</h2>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Data de Cadastro</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado_usuarios_recentes->num_rows > 0) {
                        while ($usuario = $resultado_usuarios_recentes->fetch_assoc()) {
                            $data_cadastro = date('d/m/Y H:i', strtotime($usuario['data_criacao']));
                            $status = ucfirst($usuario['status']);
                            echo "<tr>
                                    <td>{$usuario['id']}</td>
                                    <td>{$usuario['nome']}</td>
                                    <td>{$usuario['email']}</td>
                                    <td>{$data_cadastro}</td>
                                    <td>{$status}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; color: #64748b;'>Nenhum usuário cadastrado ainda</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
