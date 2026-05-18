<?php
require_once 'banco/config.php';
verificar_login();

$usuario_nome = $_SESSION['usuario_nome'];

$query = "SELECT * FROM fiados ORDER BY status = 'pendente' DESC, valor DESC, nome ASC";
$resultado = $conexao->query($query);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiados - Elite Sistema</title>
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
                <h1 class="card-title">🧾 Fiados</h1>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="api/exportar.php?tipo=fiados" class="btn btn-secondary">📥 Exportar Fiados</a>
                </div>
            </div>

            <form onsubmit="salvarDevedor(event)" class="card-body" style="margin-bottom: 2rem; display: grid; gap: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label for="nome_devedor">Nome *</label>
                        <input type="text" id="nome_devedor" placeholder="Nome do cliente" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone_devedor">Telefone</label>
                        <input type="text" id="telefone_devedor" placeholder="(XX) XXXXX-XXXX">
                    </div>
                    <div class="form-group">
                        <label for="valor_devedor">Valor (R$) *</label>
                        <input type="number" id="valor_devedor" placeholder="Ex: 250.00" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="vencimento_devedor">Vencimento</label>
                        <input type="date" id="vencimento_devedor">
                    </div>
                </div>

                <div class="form-group">
                    <label for="descricao_devedor">Descrição</label>
                    <textarea id="descricao_devedor" placeholder="Observações sobre a dívida"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Salvar fiado</button>
            </form>

            <p style="color: #64748b; margin-bottom: 1.5rem;">
                Aqui estão os fiados do sistema. Exporte para Excel quando precisar.
            </p>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Valor (R$)</th>
                        <th>Data do Fiado</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>WhatsApp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado->num_rows > 0) {
                                                while ($devedor = $resultado->fetch_assoc()) {
                                                        $valor = number_format($devedor['valor'], 2, ',', '.');
                                                        $vencimento = $devedor['data_vencimento'] ? date('d/m/Y', strtotime($devedor['data_vencimento'])) : 'N/A';
                                                        $status = ucfirst($devedor['status']);

                                                        $tel_js = addslashes($devedor['telefone']);
                                                        $nome_js = addslashes($devedor['nome']);
                                                        $valor_raw = $devedor['valor'];
                                                        $data_fiado = addslashes($vencimento);

                                                        echo "<tr>
                                                                        <td>{$devedor['id']}</td>
                                                                        <td>{$devedor['nome']}</td>
                                                                        <td>" . ($devedor['telefone'] ?: 'N/A') . "</td>
                                                                        <td>R$ {$valor}</td>
                                                                        <td>{$vencimento}</td>
                                                                        <td>" . (!empty($devedor['descricao']) ? htmlspecialchars($devedor['descricao']) : 'N/A') . "</td>
                                                                        <td>{$status}</td>
                                                                        <td><button class=\"btn btn-secondary\" onclick=\"cobrarNoWhatsApp('{$tel_js}', '{$nome_js}', '{$valor_raw}', '{$data_fiado}')\">Cobrar no WhatsApp</button></td>
                                                                    </tr>";
                                                }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center; color: #64748b;'>Nenhum fiado cadastrado ainda</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
