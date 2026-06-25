<?php

date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION)) { session_start(); }

include '../../funcoes/verifica_login.php';

include '../../funcoes/conexao.php';



$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;

if (!$idEmpresa) { die("Erro: Sessão expirada."); }



$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'dashboard';

$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'todos';



$sql_filtro = "";

if ($periodo == "hoje") $sql_filtro = " AND DATE(s.data_saida) = CURDATE()";

elseif ($periodo == "semana") $sql_filtro = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";

elseif ($periodo == "mes") $sql_filtro = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";



header('Content-Type: text/csv; charset=utf-8');

header('Content-Disposition: attachment; filename=relatorio_'.$tipo.'.csv');

$output = fopen("php://output", "w");

fwrite($output, "sep=;\n");

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));



/* =====================================================
            RELATÓRIO: DESCONTOS
            ===================================================== */
if ($tipo == "descontos") {
    // Define os cabeçalhos das colunas
    fputcsv($output, ['Produto', 'Nº Lote', 'Qtd em Estoque', 'Valor Original', 'Val. Unit. c/ Desconto', 'Desconto Aplicado', 'Valor Total Estoque'], ';');

    // Mapeamento do filtro para a tabela de lotes (l.criado_em)
    $filtro_lote = "";
    if ($periodo == "hoje") $filtro_lote = " AND DATE(l.criado_em) = CURDATE()";
    elseif ($periodo == "semana") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    elseif ($periodo == "mes") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

    $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.preco_venda, l.desconto, l.status_lote
            FROM produtoslotes l
            INNER JOIN produtos p ON p.idProduto = l.idproduto
            WHERE l.idEmpresa = '$idEmpresa' AND l.desconto > 0 $filtro_lote
            ORDER BY l.desconto DESC, p.NomeProduto ASC";
            
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $qtd = intval($row['quantidade']);
        $preco_original = floatval($row['preco_venda']);
        $porcentagem_desc = floatval($row['desconto']);
        
        $preco_com_desconto = $preco_original * (1 - ($porcentagem_desc / 100));
        $valor_total_estoque = $preco_com_desconto * $qtd;
        
        $status_limpo = strtolower(trim($row['status_lote']));
        $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';
        
        $nome_produto = $row['NomeProduto'];
        if ($status_limpo == 'vencido') {
            $nome_produto .= ' (VENCIDO)';
        }

        fputcsv($output, [
            $nome_produto,
            $lote,
            $qtd . ' un',
            'R$ ' . number_format($preco_original, 2, ',', '.'),
            'R$ ' . number_format($preco_com_desconto, 2, ',', '.'),
            number_format($porcentagem_desc, 0) . '% OFF',
            'R$ ' . number_format($valor_total_estoque, 2, ',', '.')
        ], ';');
    }
}

// 2. RELATÓRIO DE VENDAS
elseif ($tipo == "vendas") {

    fputcsv($output, ['Produto', 'Lote', 'Qtd', 'Preço Unit.', 'Desconto (%)', 'Valor Final', 'Data'], ';');

    // O $sql_filtro já está sendo injetado aqui. 
    // Certifique-se apenas de que a variável $sql_filtro esteja definida no topo do arquivo.
    $sql = "SELECT p.NomeProduto, l.numero_lote, l.preco_venda, l.desconto, s.quantidade_saida, s.data_saida
            FROM saida s
            INNER JOIN produtoslotes l ON s.idlote = l.idlote
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = '$idEmpresa' 
            AND LOWER(s.motivo_saida) = 'venda' 
            $sql_filtro 
            ORDER BY s.data_saida DESC";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $total = ($row['preco_venda'] * (1 - ($row['desconto'] / 100))) * $row['quantidade_saida'];

        fputcsv($output, [
            $row['NomeProduto'], 
            $row['numero_lote'], 
            $row['quantidade_saida'], 
            'R$ '.number_format($row['preco_venda'], 2, ',', '.'), 
            $row['desconto'].'%', 
            'R$ '.number_format($total, 2, ',', '.'), 
            $row['data_saida']
        ], ';');
    }
}

// 3. RELATÓRIO DE PRODUTOS (ALINHADO COM OS LOTES E DESCONTOS)
elseif ($tipo == "produtos") {
    // Cabeçalho do Excel com as novas colunas
    fputcsv($output, ['Produto', 'N. Lote', 'Marca', 'Preço Compra', 'Preço Venda Base', 'Desconto %', 'Preço Venda Final'], ';');

    // Definição do filtro para a tabela de lotes (l.criado_em)
    $filtro_lote = "";
    if ($periodo == "hoje") $filtro_lote = " AND DATE(l.criado_em) = CURDATE()";
    elseif ($periodo == "semana") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    elseif ($periodo == "mes") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

    $sql = "SELECT p.NomeProduto, p.MarcaProduto, l.numero_lote, l.preco_compra, l.preco_venda, l.desconto
            FROM produtoslotes l
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = '$idEmpresa' $filtro_lote
            ORDER BY p.NomeProduto ASC, l.numero_lote ASC";
            
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $p_compra = floatval($row['preco_compra']);
        $p_venda_base = floatval($row['preco_venda']);
        $desc = floatval($row['desconto']);
        
        // Calcula a venda final para o Excel
        $p_venda_final = $p_venda_base * (1 - ($desc / 100));
        $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';
        $marca = !empty($row['MarcaProduto']) ? $row['MarcaProduto'] : '-';

        // Formata os valores numéricos com vírgula para o Excel abrir certinho
        fputcsv($output, [
            $row['NomeProduto'],
            $lote,
            $marca,
            number_format($p_compra, 2, ',', '.'),
            number_format($p_venda_base, 2, ',', '.'),
            $desc . '%',
            number_format($p_venda_final, 2, ',', '.')
        ], ';');
    }
}

// 4. RELATÓRIO DE BAIXAS/PERDAS (Com cálculo de Prejuízo adicionado)
elseif ($tipo == "baixas" || $tipo == "perdas") {
    // Cabeçalho do Excel
    fputcsv($output, ['Produto', 'Lote', 'Qtd', 'Motivo', 'Custo Unitário', 'Prejuízo Total', 'Data'], ';');
    
    // O $sql_filtro já está presente na query abaixo
    $sql = "SELECT p.NomeProduto, l.numero_lote, s.quantidade_saida, s.motivo_saida, s.data_saida, l.preco_compra
            FROM saida s
            INNER JOIN produtoslotes l ON s.idlote = l.idlote
            INNER JOIN produtos p ON l.idproduto = p.idProduto
            WHERE l.idEmpresa = '$idEmpresa' 
            AND LOWER(s.motivo_saida) <> 'venda' 
            $sql_filtro 
            ORDER BY s.id_saida DESC";
            
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $qtd = intval($row['quantidade_saida']);
        $custo_unitario = floatval($row['preco_compra']);
        $prejuizo_total = $qtd * $custo_unitario;

        fputcsv($output, [
            $row['NomeProduto'], 
            $row['numero_lote'], 
            $qtd, 
            $row['motivo_saida'], 
            'R$ '.number_format($custo_unitario, 2, ',', '.'),
            'R$ '.number_format($prejuizo_total, 2, ',', '.'),
            $row['data_saida']
        ], ';');
    }
}

// 6. RELATÓRIO DE LUCRO (ALINHADO COM A INTERFACE - EXCEL LIMPO)
elseif ($tipo == "lucro") {
    // Cabeçalhos idênticos à tabela da interface
    fputcsv($output, ['Produto', 'Qtd Vendida', 'Data Venda', 'Preço Custo (Unidade)', 'Desconto', 'Valor Faturado', 'Lucro Estimado'], ';');

    // Query corrigida utilizando $sql_filtro
    $sql = "SELECT p.NomeProduto, s.quantidade_saida, s.data_saida, l.preco_compra, l.preco_venda, l.desconto
            FROM saida s
            INNER JOIN produtoslotes l ON s.idlote = l.idlote
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = '$idEmpresa' 
            AND LOWER(s.motivo_saida) = 'venda' 
            $sql_filtro 
            ORDER BY s.id_saida DESC";
            
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $data_venda = ($row['data_saida']) ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';
        $qtd = intval($row['quantidade_saida']);
        $custo_unitario = floatval($row['preco_compra']);
        $preco_venda = floatval($row['preco_venda']);
        $porcentagem_desc = floatval($row['desconto']);

        $custo_total = $qtd * $custo_unitario;
        $venda_bruta = $qtd * $preco_venda;
        $total_desconto = $venda_bruta * ($porcentagem_desc / 100);
        
        $faturamento_real = $venda_bruta - $total_desconto;
        $lucro = $faturamento_real - $custo_total;

        $texto_desconto = ($porcentagem_desc > 0) ? number_format($porcentagem_desc, 0) . '% OFF' : '-';

        fputcsv($output, [
            $row['NomeProduto'],
            $qtd . ' un',
            $data_venda,
            'R$ ' . number_format($custo_unitario, 2, ',', '.'),
            $texto_desconto,
            'R$ ' . number_format($faturamento_real, 2, ',', '.'),
            'R$ ' . number_format($lucro, 2, ',', '.')
        ], ';');
    }
}

// 4. RELATÓRIO DE LOTES (COM CAMPO MARCA)
elseif ($tipo == "lotes") {
    // Cabeçalho do Excel incluindo a coluna Marca
    fputcsv($output, ['Produto', 'Nº Lote', 'Marca', 'Quantidade', 'Validade', 'Status'], ';');

    // Definição do filtro para a tabela de lotes (l.criado_em)
    $filtro_lote = "";
    if ($periodo == "hoje") $filtro_lote = " AND DATE(l.criado_em) = CURDATE()";
    elseif ($periodo == "semana") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    elseif ($periodo == "mes") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

    $sql = "SELECT p.NomeProduto, p.MarcaProduto, l.numero_lote, l.quantidade, l.validade, l.status_lote
            FROM produtoslotes l
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = '$idEmpresa' $filtro_lote
            ORDER BY l.idlote DESC";
            
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $data_validade = ($row['validade']) ? date('d/m/Y', strtotime($row['validade'])) : '-';
        $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';
        $marca = !empty($row['MarcaProduto']) ? $row['MarcaProduto'] : '-';

        fputcsv($output, [
            $row['NomeProduto'],
            $lote,
            $marca,
            intval($row['quantidade']) . ' un',
            $data_validade,
            strtoupper($row['status_lote'])
        ], ';');
    }
}

// 5. RELATÓRIO DE VENCIMENTO (APENAS ITENS VENCIDOS)
elseif ($tipo == "vencimento") {
    // Cabeçalho do Excel com as colunas idênticas à interface
    fputcsv($output, ['Produto', 'Nº Lote', 'Quantidade', 'Validade', 'Status'], ';');

    // Definição do filtro para a tabela de lotes (l.criado_em)
    $filtro_lote = "";
    if ($periodo == "hoje") $filtro_lote = " AND DATE(l.criado_em) = CURDATE()";
    elseif ($periodo == "semana") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    elseif ($periodo == "mes") $filtro_lote = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

    // Query filtrando apenas os produtos com status 'vencido' e aplicando o filtro de data
    $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.validade, l.status_lote
            FROM produtoslotes l
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = '$idEmpresa' 
            AND LOWER(l.status_lote) = 'vencido' 
            $filtro_lote 
            ORDER BY l.idlote DESC";
            
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $data_validade = ($row['validade']) ? date('d/m/Y', strtotime($row['validade'])) : '-';
        $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';

        // Escreve os dados no arquivo CSV/Excel
        fputcsv($output, [
            $row['NomeProduto'],
            $lote,
            intval($row['quantidade']) . ' un',
            $data_validade,
            strtoupper($row['status_lote'])
        ], ';');
    }
}

// 5. DASHBOARD PADRÃO (Caso não seja nenhum dos acima)
else {

    fputcsv($output, ['Tipo', 'Produto', 'Lote', 'Qtd', 'Data', 'Motivo'], ';');

    // Adicionamos $sql_filtro ao final da cláusula WHERE
    $sql = "SELECT p.NomeProduto, l.numero_lote, s.quantidade_saida, s.data_saida, s.motivo_saida
            FROM saida s
            INNER JOIN produtoslotes l ON s.idlote = l.idlote
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = '$idEmpresa' $sql_filtro
            ORDER BY s.id_saida DESC";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['motivo_saida'], 
            $row['NomeProduto'], 
            $row['numero_lote'], 
            $row['quantidade_saida'], 
            $row['data_saida'], 
            $row['motivo_saida']
        ], ';');
    }
}





fclose($output);

exit;

?>