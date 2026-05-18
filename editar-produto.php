<?php
require_once 'banco/config.php';
verificar_login();

$usuario_nome = $_SESSION['usuario_nome'];

// Obter produto
$produto = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM produtos WHERE id = $id";
    $resultado = $conexao->query($query);
    if ($resultado->num_rows > 0) {
        $produto = $resultado->fetch_assoc();
    } else {
        header('Location: produtos.php');
        exit;
    }
} else {
    header('Location: produtos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - Elite Sistema</title>
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
                <h1 class="card-title">✏️ Editar Produto</h1>
            </div>
            
            <form onsubmit="salvarProduto(event)" style="max-width: 600px;">
                <input type="hidden" id="produto-id" value="<?php echo $produto['id']; ?>">
                
                <div class="form-group">
                    <label for="codigo_barras">Código de Barras</label>
                    <input type="text" id="codigo_barras" value="<?php echo $produto['codigo_barras']; ?>" disabled>
                    <small style="color: #64748b;">Código de barras não pode ser alterado</small>
                </div>
                
                <div class="form-group">
                    <label for="nome">Nome do Produto *</label>
                    <input type="text" id="nome" value="<?php echo $produto['nome']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao"><?php echo $produto['descricao']; ?></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="preco">Valor (R$) *</label>
                        <input type="number" id="preco" value="<?php echo $produto['preco']; ?>" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="estoque">Estoque (unidades)</label>
                        <input type="number" id="estoque" value="<?php echo $produto['estoque']; ?>">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="tamanho">Tamanho</label>
                        <input type="text" id="tamanho" value="<?php echo $produto['tamanho'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cor">Cor</label>
                        <input type="text" id="cor" value="<?php echo $produto['cor'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" value="<?php echo $produto['marca'] ?? ''; ?>">
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-sucesso">
                        ✅ Salvar Alterações
                    </button>
                    <a href="produtos.php" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- INFORMAÇÕES DO PRODUTO -->
        <div class="card">
            <h3 style="color: var(--cor-primaria); margin-bottom: 1rem;">📋 Informações do Produto</h3>
            <div class="grid" style="grid-template-columns: 1fr 1fr;">
                <div>
                    <p><strong>ID:</strong> <?php echo $produto['id']; ?></p>
                    <p><strong>Criado em:</strong> <?php echo date('d/m/Y H:i', strtotime($produto['data_criacao'])); ?></p>
                </div>
                <div>
                    <p><strong>Status:</strong> <span style="color: var(--cor-sucesso); font-weight: bold;"><?php echo ucfirst($produto['status']); ?></span></p>
                    <p><strong>Última atualização:</strong> <?php echo date('d/m/Y H:i', strtotime($produto['data_atualizacao'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
