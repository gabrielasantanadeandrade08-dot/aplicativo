<?php
// API - Exportar dados para Excel/CSV
// ===================================

require_once '../banco/config.php';
verificar_login();

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'produtos';
$usuario_id = $_SESSION['usuario_id'];

switch ($tipo) {
    case 'produtos':
        exportar_produtos();
        break;
    case 'vendas':
        exportar_vendas();
        break;
    case 'leituras':
        exportar_leituras();
        break;
    case 'fiados':
    case 'devedores':
        exportar_fiados();
        break;
    default:
        exportar_produtos();
}

function exportar_produtos() {
    global $conexao;
    
    // Headers para download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="produtos_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Abrir output como arquivo CSV
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8 (para Excel reconhecer acentuação)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Cabeçalhos
    $headers = array(
        'ID',
        'Código de Barras',
        'Nome do Produto',
        'Valor (R$)',
        'Estoque',
        'Tamanho',
        'Cor',
        'Marca',
        'Data de Criação',
        'Status'
    );
    fputcsv($output, $headers, ';');
    
    // Query de produtos
    $query = "SELECT p.* 
              FROM produtos p 
              WHERE p.status = 'ativo'
              ORDER BY p.data_criacao DESC";
    
    $resultado = $conexao->query($query);
    
    // Adicionar dados
    while ($produto = $resultado->fetch_assoc()) {
        $row = array(
            $produto['id'],
            $produto['codigo_barras'],
            $produto['nome'],
            number_format($produto['preco'], 2, ',', '.'),
            $produto['estoque'],
            $produto['tamanho'] ?? 'N/A',
            $produto['cor'] ?? 'N/A',
            $produto['marca'] ?? 'N/A',
            date('d/m/Y H:i', strtotime($produto['data_criacao'])),
            ucfirst($produto['status'])
        );
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
}

function exportar_vendas() {
    global $conexao, $usuario_id;
    
    // Headers para download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="vendas_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Cabeçalhos
    $headers = array(
        'ID da Venda',
        'Data da Venda',
        'Total (R$)',
        'Método de Pagamento',
        'Produto',
        'Quantidade',
        'Preço Unitário',
        'Subtotal'
    );
    fputcsv($output, $headers, ';');
    
    // Query de vendas
    $query = "SELECT v.*, vi.*, p.nome as produto_nome 
              FROM vendas v 
              LEFT JOIN vendas_itens vi ON v.id = vi.venda_id 
              LEFT JOIN produtos p ON vi.produto_id = p.id 
              WHERE v.usuario_id = $usuario_id
              ORDER BY v.data_venda DESC";
    
    $resultado = $conexao->query($query);
    
    // Adicionar dados
    while ($venda = $resultado->fetch_assoc()) {
        $row = array(
            $venda['id'],
            date('d/m/Y H:i', strtotime($venda['data_venda'])),
            number_format($venda['total'], 2, ',', '.'),
            $venda['metodo_pagamento'] ?? 'N/A',
            $venda['produto_nome'] ?? 'N/A',
            $venda['quantidade'] ?? 'N/A',
            isset($venda['preco_unitario']) ? number_format($venda['preco_unitario'], 2, ',', '.') : 'N/A',
            isset($venda['subtotal']) ? number_format($venda['subtotal'], 2, ',', '.') : 'N/A'
        );
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
}

function exportar_leituras() {
    global $conexao, $usuario_id;
    
    // Headers para download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="leituras_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Cabeçalhos
    $headers = array(
        'ID',
        'Data/Hora',
        'Código de Barras',
        'Produto',
        'Status'
    );
    fputcsv($output, $headers, ';');
    
    // Query de leituras
    $query = "SELECT l.*, p.nome as produto_nome 
              FROM leitura_barras_log l 
              LEFT JOIN produtos p ON l.produto_id = p.id 
              WHERE l.usuario_id = $usuario_id
              ORDER BY l.data_leitura DESC 
              LIMIT 1000";
    
    $resultado = $conexao->query($query);
    
    // Adicionar dados
    while ($leitura = $resultado->fetch_assoc()) {
        $status = $leitura['sucesso'] ? 'Encontrado' : 'Não encontrado';
        $row = array(
            $leitura['id'],
            date('d/m/Y H:i:s', strtotime($leitura['data_leitura'])),
            $leitura['codigo_barras'],
            $leitura['produto_nome'] ?? 'N/A',
            $status
        );
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
}

function exportar_fiados() {
    global $conexao;

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="fiados_' . date('Y-m-d_H-i-s') . '.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    $headers = array(
        'ID',
        'Nome',
        'Telefone',
        'Valor (R$)',
        'Data de Vencimento',
        'Descrição',
        'Status'
    );
    fputcsv($output, $headers, ';');

    $query = "SELECT * FROM fiados WHERE status = 'pendente' ORDER BY valor DESC, nome ASC";
    $resultado = $conexao->query($query);

    while ($fiado = $resultado->fetch_assoc()) {
        $row = array(
            $fiado['id'],
            $fiado['nome'],
            $fiado['telefone'] ?? 'N/A',
            number_format($fiado['valor'], 2, ',', '.'),
            $fiado['data_vencimento'] ? date('d/m/Y', strtotime($fiado['data_vencimento'])) : 'N/A',
            $fiado['descricao'] ?? 'N/A',
            ucfirst($fiado['status'])
        );
        fputcsv($output, $row, ';');
    }

    fclose($output);
    exit;
}
?>
