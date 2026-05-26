<?php
require_once 'banco/config.php';
verificar_login();
$usuario_nome = $_SESSION['usuario_nome'];
$usuario_email = $_SESSION['usuario_email'];
$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT telefone, endereco, data_nascimento, foto_perfil FROM usuarios WHERE id = $usuario_id";
$tel = $endereco = $data_nasc = $foto_perfil = '';
$res = $conexao->query($query);
if ($res && $row = $res->fetch_assoc()) {
    $tel = $row['telefone'];
    $endereco = $row['endereco'];
    $data_nasc = $row['data_nascimento'];
    $foto_perfil = $row['foto_perfil'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">🏪 Elite Sistema</div>
        <div class="navbar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="produtos.php">Produtos</a>
            <a href="devedores.php">Fiados</a>
            <a href="painel-leitor.php">Leitor de Código</a>
            <a href="perfil.php" class="btn btn-secondary">Meu Perfil</a>
            <span><?php echo $usuario_nome; ?></span>
            <a href="banco/config.php?logout=1" class="btn-sair">Sair</a>
        </div>
    </nav>
    <div class="container">
        <div class="card" style="max-width: 500px; margin: 2rem auto;">
            <h1 class="card-title" style="margin-bottom: 1.5rem;">Meu Perfil</h1>
            <form id="form-perfil" class="card-body" style="display: grid; gap: 1.2rem;" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="endereco">Endereço</label>
                                    <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($endereco); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="data_nascimento">Data de Nascimento</label>
                                    <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($data_nasc); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="foto_perfil">Foto de Perfil</label>
                                    <?php if ($foto_perfil): ?>
                                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de perfil" style="max-width:80px;max-height:80px;border-radius:50%;display:block;margin-bottom:0.5rem;">
                                    <?php endif; ?>
                                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                                </div>
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_nome); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($tel); ?>">
                </div>
                <div class="form-group">
                    <label for="senha">Nova Senha <span style="color:#64748b;font-size:0.9em;">(deixe em branco para não alterar)</span></label>
                    <input type="password" id="senha" name="senha" minlength="6">
                </div>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </form>
            <hr style="margin:2rem 0;">
            <form id="form-excluir" style="margin-top:1.5rem;">
                <div class="form-group">
                    <label for="senha_excluir">Digite sua senha para excluir a conta</label>
                    <input type="password" id="senha_excluir" name="senha_excluir" required minlength="6">
                </div>
                <button type="submit" class="btn btn-erro" style="width:100%;">Excluir minha conta</button>
            </form>
            <div id="msg-perfil" style="margin-top:1.5rem;"></div>
        </div>
    </div>
    <script>
    document.getElementById('form-perfil').addEventListener('submit', function(e){
        e.preventDefault();
        var data = new FormData(this);
        data.append('acao','editar_usuario');
        fetch('api/auth.php', { method:'POST', body:data, credentials:'same-origin' })
        .then(r=>r.json()).then(json=>{
            var el = document.getElementById('msg-perfil');
            if(json.status==='sucesso'){
                el.innerHTML = '<div class="alert alert-sucesso">'+json.mensagem+'</div>';
            }else{
                el.innerHTML = '<div class="alert alert-erro">'+json.mensagem+'</div>';
            }
        });
    });
    document.getElementById('form-excluir').addEventListener('submit', function(e){
        e.preventDefault();
        var senha = document.getElementById('senha_excluir').value;
        if(!senha || senha.length < 6){
            document.getElementById('msg-perfil').innerHTML = '<div class="alert alert-erro">Digite sua senha corretamente.</div>';
            return;
        }
        var data = new FormData();
        data.append('acao','excluir_usuario');
        data.append('senha', senha);
        fetch('api/auth.php', { method:'POST', body:data, credentials:'same-origin' })
        .then(r=>r.json()).then(json=>{
            var el = document.getElementById('msg-perfil');
            if(json.status==='sucesso'){
                el.innerHTML = '<div class="alert alert-sucesso">'+json.mensagem+'</div>';
                setTimeout(function(){ window.location.href='login.php'; }, 2000);
            }else{
                el.innerHTML = '<div class="alert alert-erro">'+json.mensagem+'</div>';
            }
        });
    });
    </script>
</body>
</html>