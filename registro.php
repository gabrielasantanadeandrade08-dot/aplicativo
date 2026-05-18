<?php
// Verificar se já está logado
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Elite Sistema</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">📝 Criar Conta</h1>
            
            <form onsubmit="fazerRegistro(event)">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" placeholder="Seu nome completo" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="seu@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" placeholder="Mínimo 6 caracteres" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" placeholder="Confirme sua senha" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Criar Conta
                </button>
            </form>
            
            <div class="auth-link">
                Já tem conta? <a href="login.php">Faça login aqui</a>
            </div>
        </div>
    </div>
    
    <script src="js/app.js"></script>
</body>
</html>
