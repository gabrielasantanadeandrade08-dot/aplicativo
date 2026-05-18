<?php
// API - Devedores
// ==============

require_once '../banco/config.php';
verificar_login();

$acao = isset($_POST['acao']) ? $_POST['acao'] : (isset($_GET['acao']) ? $_GET['acao'] : '');

switch ($acao) {
    case 'listar':
        listar_fiados();
        break;
    case 'criar':
        criar_fiado();
        break;
    case 'marcar_whatsapp':
        marcar_whatsapp();
        break;
    default:
        json_response('erro', 'Ação não especificada');
}

function listar_fiados() {
    global $conexao;

    $query = "SELECT * FROM fiados ORDER BY status = 'pendente' DESC, valor DESC, nome ASC";
    $resultado = $conexao->query($query);

    $fiados = [];
    while ($fiado = $resultado->fetch_assoc()) {
        // Garante que o campo interagiu_whatsapp sempre exista no array
        if (!isset($fiado['interagiu_whatsapp'])) {
            $fiado['interagiu_whatsapp'] = false;
        }
        $fiados[] = $fiado;
    }

    json_response('sucesso', 'Fiados listados', $fiados);
}

function criar_fiado() {
    global $conexao;

    $nome = isset($_POST['nome']) ? escapar($_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? escapar($_POST['telefone']) : '';
    $valor = isset($_POST['valor']) ? (float)$_POST['valor'] : 0;
    $data_vencimento = isset($_POST['data_vencimento']) ? escapar($_POST['data_vencimento']) : null;
    $descricao = isset($_POST['descricao']) ? escapar($_POST['descricao']) : '';
    $interagiu_whatsapp = isset($_POST['interagiu_whatsapp']) ? (int)$_POST['interagiu_whatsapp'] : 0;

    if (empty($nome) || $valor <= 0) {
        json_response('erro', 'Nome e valor são obrigatórios');
    }

    if (!empty($data_vencimento) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_vencimento)) {
        json_response('erro', 'Data de vencimento inválida');
    }

    $data_vencimento_sql = $data_vencimento ? "'$data_vencimento'" : 'NULL';

    $query = "INSERT INTO fiados (nome, telefone, valor, data_vencimento, descricao, interagiu_whatsapp, status) VALUES ('$nome', '$telefone', $valor, $data_vencimento_sql, '$descricao', $interagiu_whatsapp, 'pendente')";

    if ($conexao->query($query)) {
        json_response('sucesso', 'Fiado adicionado com sucesso', ['id' => $conexao->insert_id]);
    } else {
        json_response('erro', 'Erro ao salvar fiado: ' . $conexao->error);
    }
}

function marcar_whatsapp() {
    global $conexao;

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        json_response('erro', 'ID inválido');
    }

    $query = "UPDATE fiados SET interagiu_whatsapp = 1 WHERE id = $id";
    if ($conexao->query($query)) {
        json_response('sucesso', 'Fiado marcado como contatado via WhatsApp');
    } else {
        json_response('erro', 'Erro ao atualizar: ' . $conexao->error);
    }
}
?>