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



// 1. RELATÓRIO DE DESCONTOS

if ($tipo == "descontos") {

    fputcsv($output, ['Produto', 'Lote', 'Quantidade', 'Desconto (%)', 'Valor Desconto (R$)'], ';');

    $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.desconto, l.preco_venda

            FROM produtoslotes l

            INNER JOIN produtos p ON p.idProduto = l.idproduto

            WHERE l.idEmpresa = '$idEmpresa' AND l.desconto > 0";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {

        $valor_desc = ($row['quantidade'] * $row['preco_venda']) * ($row['desconto'] / 100);

        fputcsv($output, [$row['NomeProduto'], $row['numero_lote'], $row['quantidade'].' un', $row['desconto'].'%', number_format($valor_desc, 2, ',', '.')], ';');

    }

}

// 2. RELATÓRIO DE VENDAS

elseif ($tipo == "vendas") {

    fputcsv($output, ['Produto', 'Lote', 'Qtd', 'Preço Unit.', 'Desconto (%)', 'Valor Final', 'Data'], ';');

    $sql = "SELECT p.NomeProduto, l.numero_lote, l.preco_venda, l.desconto, s.quantidade_saida, s.data_saida

            FROM saida s

            INNER JOIN produtoslotes l ON s.idlote = l.idlote

            INNER JOIN produtos p ON l.idproduto = p.IdProduto

            WHERE l.idEmpresa = '$idEmpresa' AND LOWER(s.motivo_saida) = 'venda' $sql_filtro ORDER BY s.id_saida DESC";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {

        $total = ($row['preco_venda'] * (1 - ($row['desconto'] / 100))) * $row['quantidade_saida'];

        fputcsv($output, [$row['NomeProduto'], $row['numero_lote'], $row['quantidade_saida'], 'R$ '.number_format($row['preco_venda'], 2, ',', '.'), $row['desconto'].'%', 'R$ '.number_format($total, 2, ',', '.'), $row['data_saida']], ';');

    }

}

// 3. RELATÓRIO DE PRODUTOS

elseif ($tipo == "produtos") {

    fputcsv($output, ['Produto', 'Marca', 'Preço Compra', 'Preço Venda'], ';');

    $sql = "SELECT NomeProduto, MarcaProduto, preco_padrao_compra, preco_padrao_venda FROM produtos WHERE idEmpresa = '$idEmpresa'";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {

        fputcsv($output, [$row['NomeProduto'], $row['MarcaProduto'], $row['preco_padrao_compra'], $row['preco_padrao_venda']], ';');

    }

}

// 4. RELATÓRIO DE BAIXAS/PERDAS

elseif ($tipo == "baixas" || $tipo == "perdas") {

    fputcsv($output, ['Produto', 'Lote', 'Qtd', 'Motivo', 'Data'], ';');

    $sql = "SELECT p.NomeProduto, l.numero_lote, s.quantidade_saida, s.motivo_saida, s.data_saida

            FROM saida s

            INNER JOIN produtoslotes l ON s.idlote = l.idlote

            INNER JOIN produtos p ON l.idproduto = p.IdProduto

            WHERE l.idEmpresa = '$idEmpresa' AND LOWER(s.motivo_saida) <> 'venda'";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {

        fputcsv($output, [$row['NomeProduto'], $row['numero_lote'], $row['quantidade_saida'], $row['motivo_saida'], $row['data_saida']], ';');

    }

}

// 6. RELATÓRIO DE LUCRO (Adicionado para o Excel)

elseif ($tipo == "lucro") {

    fputcsv($output, ['Produto', 'Custo', 'Venda', 'Desconto (%)', 'Lucro Estimado (R$)'], ';');

   

    // Consulta buscando o desconto na tabela produtoslotes

    $sql = "SELECT p.NomeProduto, p.preco_padrao_compra, p.preco_padrao_venda, l.desconto

            FROM produtos p

            LEFT JOIN produtoslotes l ON p.idProduto = l.idproduto

            WHERE p.idEmpresa = '$idEmpresa'

            ORDER BY p.NomeProduto ASC";

           

    $result = $conn->query($sql);

   

    while ($row = $result->fetch_assoc()) {

        $desconto = isset($row['desconto']) ? $row['desconto'] : 0;

        $preco_venda_final = $row['preco_padrao_venda'] * (1 - ($desconto / 100));

        $lucro = $preco_venda_final - $row['preco_padrao_compra'];

       

        fputcsv($output, [

            $row['NomeProduto'],

            number_format($row['preco_padrao_compra'], 2, ',', '.'),

            number_format($row['preco_padrao_venda'], 2, ',', '.'),

            $desconto.'%',

            number_format($lucro, 2, ',', '.')

        ], ';');

    }

}

// 5. DASHBOARD PADRÃO (Caso não seja nenhum dos acima)

else {

    fputcsv($output, ['Tipo', 'Produto', 'Lote', 'Qtd', 'Data', 'Motivo'], ';');

    $sql = "SELECT p.NomeProduto, l.numero_lote, s.quantidade_saida, s.data_saida, s.motivo_saida

            FROM saida s

            INNER JOIN produtoslotes l ON s.idlote = l.idlote

            INNER JOIN produtos p ON l.idproduto = p.IdProduto

            WHERE l.idEmpresa = '$idEmpresa'";

    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {

        fputcsv($output, [$row['motivo_saida'], $row['NomeProduto'], $row['numero_lote'], $row['quantidade_saida'], $row['data_saida'], $row['motivo_saida']], ';');

    }

}



fclose($output);

exit;

?>