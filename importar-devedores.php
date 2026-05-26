<?php
require_once 'banco/config.php';
verificar_login();
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Importar Devedores - Elite Sistema</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Importar Devedores (CSV)</h1>
    <p>Formato esperado: nome,telefone,valor,data_vencimento,descricao (primeira linha opcionalmente cabeçalho)</p>

    <form id="form-import" action="api/importar_devedores.php" method="post" enctype="multipart/form-data">
        <label>Arquivo CSV: <input type="file" name="csv" accept=".csv,text/csv" required></label>
        <br><br>
        <button type="submit">Enviar e Importar</button>
    </form>

    <div id="resultado" style="margin-top:20px;"></div>
</div>

<script>
document.getElementById('form-import').addEventListener('submit', function(e){
    e.preventDefault();
    var form = e.target;
    var data = new FormData(form);

    fetch(form.action, { method: 'POST', body: data, credentials: 'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(json){
            var el = document.getElementById('resultado');
            if(json.status === 'sucesso'){
                el.innerHTML = '<strong>Importação concluída:</strong> ' + (json.dados.inserted || 0) + ' registros importados.';
            } else {
                el.innerHTML = '<strong>Erro:</strong> ' + json.mensagem;
            }
        }).catch(function(err){
            document.getElementById('resultado').innerText = 'Erro na requisição: ' + err.message;
        });
});
</script>
</body>
</html>
