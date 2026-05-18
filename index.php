<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Verificar se é primeira execução
$banco_config = __DIR__ . '/banco/config.php';
if (!isset($_GET['setup_skip'])) {
    $conexao_teste = @new mysqli('localhost', 'root', '', 'elite_sistema');
    if ($conexao_teste->connect_error) {
        header('Location: banco/setup.php');
        exit;
    }
    $conexao_teste->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Sistema - Loja de Roupas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">🏪 Elite Sistema</div>
        <div class="navbar-menu">
            <a href="login.php" class="btn btn-primary">Entrar</a>
            <a href="registro.php" class="btn btn-secondary">Cadastro</a>
        </div>
    </nav>

    <div class="container hero">
        <div style="text-align: center; max-width: 900px; margin: 0 auto;">
            <h1 class="hero-title">
                🏪 Elite Sistema
            </h1>
            
            <p class="hero-subtitle">
                Sua solução completa de gerenciamento para lojas de roupas e camisetas de time
            </p>
            
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary btn-lg">Entrar</a>
                <a href="registro.php" class="btn btn-secondary btn-lg">Cadastro</a>
            </div>
            
            <div class="feature-list">
                <div class="grid-item">
                    <h3>📦 Gestão Completa</h3>
                    <p>Cadastre e gerencie todos os seus produtos com facilidade</p>
                </div>
                
                <div class="grid-item">
                    <h3>🔍 Leitor de Código</h3>
                    <p>Leia código de barras e encontre produtos instantaneamente</p>
                </div>
                
                <div class="grid-item">
                    <h3>📊 Dashboard Profissional</h3>
                    <p>Acompanhe vendas, estoque e estatísticas em tempo real</p>
                </div>
            </div>
            
            <div class="hero-card">
                <h2 style="color: var(--cor-primaria); margin-bottom: 1rem;">✨ Características Principais</h2>
                <ul class="feature-list" style="margin-top: 0;">
                    <li>✅ Autenticação segura com criptografia</li>
                    <li>✅ Gerenciamento de produtos com código de barras</li>
                    <li>✅ Leitor de código integrado</li>
                    <li>✅ Painel administrativo intuitivo</li>
                    <li>✅ Controle de estoque</li>
                    <li>✅ Histórico de leituras</li>
                    <li>✅ Exportação e relatórios</li>
                </ul>
            </div>
            
            <div class="hero-card note-card">
                <h3 style="color: var(--cor-primaria);">🎯 Comece Agora!</h3>
                <p style="color: #0c4a6e; margin-bottom: 1rem;">
                    Acesse com as credenciais demo para explorar o sistema:
                </p>
                <p style="color: #0c4a6e;">
                    <strong>Email:</strong> admin@elite.com<br>
                    <strong>Senha:</strong> 123456
                </p>
            </div>
        </div>
    </div>

    <footer style="text-align: center; padding: 2rem; color: #64748b; border-top: 1px solid var(--cor-bordas); margin-top: 4rem;">
        <p>&copy; 2024 Elite Sistema - Todos os direitos reservados</p>
    </footer>

    <script src="js/app.js"></script>
</body>
</html>
