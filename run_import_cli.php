<?php
// Script de execução local para testar a importação via CLI.
chdir(__DIR__);
// Carregar sessão e simular usuário logado
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['usuario_id'] = 1;
// Simular método POST
$_SERVER['REQUEST_METHOD'] = 'POST';
// Simular arquivo enviado
$csvPath = __DIR__ . '/test_devedores.csv';
$_FILES['csv'] = [
    'name' => 'test_devedores.csv',
    'type' => 'text/csv',
    'tmp_name' => $csvPath,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($csvPath)
];

// Incluir o endpoint de importação
require __DIR__ . '/api/importar_devedores.php';

// O endpoint envia JSON e encerra o script via json_response()
