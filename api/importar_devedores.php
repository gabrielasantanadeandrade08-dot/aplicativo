<?php
require_once __DIR__ . '/../banco/config.php';
verificar_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response('erro', 'Método inválido, use POST');
}

if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
    json_response('erro', 'Arquivo CSV não enviado ou erro no upload');
}

$tmp = $_FILES['csv']['tmp_name'];
$content = file_get_contents($tmp);
if ($content === false) {
    json_response('erro', 'Não foi possível ler o arquivo');
}

// Detecta delimitador (',' ou ';') usando a primeira linha
$firstLine = strtok($content, "\n");
$delimiter = strpos($firstLine, ';') !== false ? ';' : ',';

$lines = array_filter(array_map('trim', explode("\n", $content)));
if (count($lines) === 0) {
    json_response('erro', 'Arquivo vazio');
}

// Preparar inserções
global $conexao;
$conexao->begin_transaction();
$inserted = 0;
$errors = [];
$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;

foreach ($lines as $i => $line) {
    // Pula linha vazia
    if (trim($line) === '') continue;

    $row = str_getcsv($line, $delimiter);

    // Detecta e pula header (se a primeira coluna contiver letras "nome" ou similar)
    if ($i === 0) {
        $first = isset($row[0]) ? strtolower($row[0]) : '';
        if (strpos($first, 'nome') !== false || strpos($first, 'telefone') !== false) {
            continue; // pula header
        }
    }

    $nome = isset($row[0]) ? escapar($row[0]) : '';
    $telefone = isset($row[1]) ? escapar($row[1]) : '';
    $valor_raw = isset($row[2]) ? $row[2] : '0';
    // Normaliza valor: tira R$, espaços e troca vírgula por ponto
    $valor = floatval(str_replace(["R$"," ","\n","\r"], ['', '', '', ''], str_replace(',', '.', $valor_raw)));
    $data_vencimento = isset($row[3]) && trim($row[3]) !== '' ? escapar($row[3]) : null;
    $descricao = isset($row[4]) ? escapar($row[4]) : '';

    if ($nome === '' || $valor <= 0) {
        // Nome e valor são obrigatórios; registra erro e pula
        $errors[] = ['linha' => $i+1, 'mensagem' => 'Nome vazio ou valor inválido'];
        continue;
    }

    $data_sql = $data_vencimento ? "'{$data_vencimento}'" : 'NULL';

    $query = "INSERT INTO fiados (nome, telefone, valor, data_vencimento, descricao, usuario_id, interagiu_whatsapp, status) VALUES ('{$nome}', '{$telefone}', {$valor}, {$data_sql}, '{$descricao}', " . ($usuario_id !== null ? $usuario_id : 'NULL') . ", 0, 'pendente')";

    if ($conexao->query($query)) {
        $inserted++;
    } else {
        $errors[] = ['linha' => $i+1, 'mensagem' => $conexao->error];
    }
}

if (count($errors) === 0) {
    $conexao->commit();
    json_response('sucesso', 'Importação concluída', ['inserted' => $inserted]);
} else {
    $conexao->rollback();
    json_response('erro', 'Ocorreram erros durante a importação', ['inserted' => $inserted, 'errors' => $errors]);
}

?>
