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
    <title>Login - Elite Sistema</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1 class="auth-title">🔐 Elite Sistema</h1>
            
            <form onsubmit="fazerLogin(event)">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="seu@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" placeholder="Sua senha" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Entrar
                </button>
            </form>
            
            <div class="auth-link">
                Não tem conta? <a href="registro.php">Cadastre-se aqui</a>
            </div>
            
            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--cor-bordas);">
            
            <div style="text-align: center; font-size: 0.9rem; color: #64748b;">
                <p><strong>Credenciais Demo:</strong></p>
                <p>Email: admin@elite.com</p>
                <p>Senha: 123456</p>
            </div>
        </div>
    </div>
    
    <script src="js/app.js"></script>

    <footer style="margin-top:2rem; text-align:center; color:#64748b; font-size:0.95rem;">
        Elite Sistema &copy; <?php echo date('Y'); ?> &mdash; Versão 2.8.9<br>
        Dúvidas ou suporte? Fale no WhatsApp: <a href="https://wa.me/5599999999999" style="color:#0f766e;text-decoration:underline;">(99) 99999-9999</a>
    </footer>
</body>
</html>
