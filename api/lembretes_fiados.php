<?php
// API - Lembretes de Fiados
// Retorna fiados vencendo em N dias (padrão: hoje)

require_once '../banco/config.php';
verificar_login();

$dias = isset($_GET['dias']) ? (int)$_GET['dias'] : 0;

// Calcula a data alvo (hoje + dias)
$query = "SELECT * FROM fiados WHERE status = 'pendente' AND data_vencimento IS NOT NULL AND data_vencimento = DATE_ADD(CURDATE(), INTERVAL $dias DAY) ORDER BY data_vencimento ASC, valor DESC";

$resultado = $conexao->query($query);

$fiados = [];
if ($resultado) {
    while ($row = $resultado->fetch_assoc()) {
        if (!isset($row['interagiu_whatsapp'])) $row['interagiu_whatsapp'] = false;
        $fiados[] = $row;
    }
}

json_response('sucesso', 'Lembretes obtidos', $fiados);

?>
