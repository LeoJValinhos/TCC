<?php
// Inicia a sessão para identificar a empresa logada
if (!isset($_SESSION)) {
    session_start();
}

include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;

if (!$idEmpresa) {
    die("Erro: Sessão expirada. Faça login novamente.");
}

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'produtos';

// Força o download no formato estrito de .csv com codificação UTF-8
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=relatorio_'.$tipo.'.csv');
header('Cache-Control: max-age=0');

// Abre a saída de dados do PHP
$output = fopen("php://output", "w");

// TRUQUE DO EXCEL: Diz explicitamente para o Excel usar ponto e vírgula como separador de colunas
fwrite($output, "sep=;\n");

// Garante acentuação e caracteres especiais corretos no Excel (BOM UTF-8)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

/* =====================================================
RELATÓRIO: PRODUTOS
===================================================== */
if ($tipo == "produtos") {
    // Cabeçalho das colunas separado por ponto e vírgula
    fputcsv($output, ['Produto', 'Marca', 'Preço Compra', 'Preço Venda'], ';');

    $sql = "SELECT NomeProduto, MarcaProduto, preco_padrao_compra, preco_padrao_venda FROM produtos WHERE idEmpresa = $idEmpresa ORDER BY NomeProduto ASC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['NomeProduto'], 
            $row['MarcaProduto'], 
            'R$ ' . number_format($row['preco_padrao_compra'], 2, ',', '.'), 
            'R$ ' . number_format($row['preco_padrao_venda'], 2, ',', '.')
        ], ';');
    }
}

/* =====================================================
RELATÓRIO: LOTES
===================================================== */
elseif ($tipo == "lotes") {
    fputcsv($output, ['Produto', 'Lote', 'Quantidade', 'Validade', 'Status'], ';');

    $sql = "SELECT l.numero_lote, l.quantidade, l.validade, l.status_lote, p.NomeProduto FROM produtoslotes l INNER JOIN produtos p ON p.idProduto = l.idproduto WHERE l.idEmpresa = $idEmpresa ORDER BY p.NomeProduto ASC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $val = $row['validade'] ? date('d/m/Y', strtotime($row['validade'])) : 'N/A';
        fputcsv($output, [
            $row['NomeProduto'], 
            $row['numero_lote'], 
            $row['quantidade'] . ' un', 
            $val, 
            strtoupper($row['status_lote'])
        ], ';');
    }
}

/* =====================================================
RELATÓRIO: VENCIMENTO
===================================================== */
elseif ($tipo == "vencimento") {
    fputcsv($output, ['Produto', 'Lote', 'Quantidade', 'Data Vencimento'], ';');

    $sql = "SELECT l.numero_lote, l.quantidade, l.validade, p.NomeProduto FROM produtoslotes l INNER JOIN produtos p ON p.idProduto = l.idproduto WHERE l.idEmpresa = $idEmpresa ORDER BY l.validade ASC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $val = $row['validade'] ? date('d/m/Y', strtotime($row['validade'])) : 'N/A';
        fputcsv($output, [
            $row['NomeProduto'], 
            $row['numero_lote'], 
            $row['quantidade'] . ' un', 
            $val
        ], ';');
    }
}

/* =====================================================
RELATÓRIO: DESCONTOS
===================================================== */
elseif ($tipo == "descontos") {
    fputcsv($output, ['Produto', 'Lote', 'Quantidade', 'Desconto'], ';');

    $sql = "SELECT l.numero_lote, l.quantidade, l.desconto, p.NomeProduto FROM produtoslotes l INNER JOIN produtos p ON p.idProduto = l.idproduto WHERE l.idEmpresa = $idEmpresa AND l.desconto > 0 ORDER BY l.desconto DESC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['NomeProduto'], 
            $row['numero_lote'], 
            $row['quantidade'] . ' un', 
            $row['desconto'] . '% OFF'
        ], ';');
    }
}

/* =====================================================
RELATÓRIO: LUCRO
===================================================== */
elseif ($tipo == "lucro") {
    fputcsv($output, ['Produto', 'Custo Compra', 'Preço Venda', 'Margem Estimada'], ';');

    $sql = "SELECT NomeProduto, preco_padrao_compra, preco_padrao_venda, (preco_padrao_venda - preco_padrao_compra) AS lucro_unitario FROM produtos WHERE idEmpresa = $idEmpresa ORDER BY lucro_unitario DESC";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['NomeProduto'], 
            'R$ ' . number_format($row['preco_padrao_compra'], 2, ',', '.'), 
            'R$ ' . number_format($row['preco_padrao_venda'], 2, ',', '.'), 
            'R$ ' . number_format($row['lucro_unitario'], 2, ',', '.')
        ], ';');
    }
}

fclose($output);
exit;