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
    <title>Novo Produto - Elite Sistema</title>
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
                <h1 class="card-title">➕ Novo Produto</h1>
            </div>
            
            <form onsubmit="salvarProduto(event)" style="max-width: 600px;">
                <input type="hidden" id="produto-id" value="">
                
                <div class="form-group">
                    <label for="codigo_barras">Código de Barras *</label>
                    <input type="text" id="codigo_barras" placeholder="Ex: 7891234567890" required>
                </div>
                
                <div class="form-group">
                    <label for="nome">Nome do Produto *</label>
                    <input type="text" id="nome" placeholder="Ex: Camisa Flamengo 2024" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" placeholder="Descrição detalhada do produto"></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="preco">Valor (R$) *</label>
                        <input type="number" id="preco" placeholder="Ex: 189.90" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="estoque">Estoque (unidades)</label>
                        <input type="number" id="estoque" placeholder="Ex: 15" value="0">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="tamanho">Tamanho</label>
                        <input type="text" id="tamanho" placeholder="Ex: M, G, 40">
                    </div>
                    
                    <div class="form-group">
                        <label for="cor">Cor</label>
                        <input type="text" id="cor" placeholder="Ex: Vermelho, Azul">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" placeholder="Ex: Adidas, Puma">
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-sucesso">
                        ✅ Salvar Produto
                    </button>
                    <a href="produtos.php" class="btn btn-secundaria">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- DICA SOBRE CÓDIGO DE BARRAS -->
        <div class="card">
            <h3 style="color: var(--cor-primaria); margin-bottom: 1rem;">💡 Dicas de Código de Barras</h3>
            <ul style="color: #64748b; line-height: 2;">
                <li>✓ Use códigos únicos para cada produto</li>
                <li>✓ Formato comum: EAN-13 (13 dígitos)</li>
                <li>✓ Exemplo: 7891234567890</li>
                <li>✓ O código será usado para busca rápida no leitor</li>
                <li>✓ Você pode gerar códigos em geradores online</li>
            </ul>
        </div>
    </div>

    <script src="js/app.js"></script>
</body>
</html>
