<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$usuario = 'root';
$senha = 'usbw'; 
$banco = 'databasetcc'; 

$conexao = mysqli_connect('localhost', $usuario, $senha, $banco);
if (!$conexao) {
    $conexao = mysqli_connect('127.0.0.1', $usuario, $senha, $banco);
}

if (!$conexao) {
    echo json_encode(['total' => 0, 'alertas' => []]);
    exit;
}

// 1. Busca os dias de antecedência da configuração
$dias_limite = 30; 
$query_config = "SELECT dias_antecedencia_vencimento FROM configuracoes_alertas LIMIT 1";
$res_config = mysqli_query($conexao, $query_config);
if ($res_config && $linha_config = mysqli_fetch_assoc($res_config)) {
    $dias_limite = (int)$linha_config['dias_antecedencia_vencimento'];
}

$alertas_completos = [];
$total_real_alertas = 0; // Vai guardar a contagem real

// 2. REGRA 1: BUSCA TODOS OS VENCIDOS / VENCENDO (Filtrando os que foram ocultados permanentemente)
$query_vencimento = "SELECT p.idProduto, p.NomeProduto, l.validade, l.quantidade, l.numero_lote 
                     FROM produtoslotes l
                     INNER JOIN produtos p ON l.idproduto = p.idProduto
                     WHERE l.validade <= DATE_ADD(CURDATE(), INTERVAL $dias_limite DAY)
                       AND NOT EXISTS (
                           SELECT 1 FROM alertas_ocultos ao 
                           WHERE ao.idProduto = p.idProduto 
                             AND ao.tipo_alerta = 'vencimento'
                             AND (ao.numero_lote = l.numero_lote OR ao.numero_lote IS NULL OR ao.numero_lote = '')
                       )
                     ORDER BY l.validade ASC";

$res_vencimento = mysqli_query($conexao, $query_vencimento);
if ($res_vencimento) {
    while ($linha = mysqli_fetch_assoc($res_vencimento)) {
        $tipo = (strtotime($linha['validade']) < strtotime(date('Y-m-d'))) ? 'vencido' : 'vencendo';

        $alertas_completos[] = [
            'idProduto' => $linha['idProduto'],
            'NomeProduto' => $linha['NomeProduto'],
            'validade' => $linha['validade'],
            'quantidade' => $linha['quantidade'],
            'tipo' => $tipo
        ];
        $total_real_alertas++; 
    }
}

// 3. REGRA 2: BUSCA TODOS COM ESTOQUE MÍNIMO (Filtrando os que foram ocultados permanentemente)
$query_estoque = "SELECT p.idProduto, p.NomeProduto, p.estoque_minimo, COALESCE(SUM(l.quantidade), 0) as total_atual
                  FROM produtos p
                  LEFT JOIN produtoslotes l ON p.idProduto = l.idproduto
                  WHERE NOT EXISTS (
                      SELECT 1 FROM alertas_ocultos ao 
                      WHERE ao.idProduto = p.idProduto AND ao.tipo_alerta = 'estoque'
                  )
                  GROUP BY p.idProduto, p.NomeProduto, p.estoque_minimo
                  HAVING total_atual <= p.estoque_minimo AND p.estoque_minimo > 0";

$res_estoque = mysqli_query($conexao, $query_estoque);
if ($res_estoque) {
    while ($linha = mysqli_fetch_assoc($res_estoque)) {
        $alertas_completos[] = [
            'idProduto' => $linha['idProduto'],
            'NomeProduto' => $linha['NomeProduto'],
            'validade' => null,
            'quantidade' => $linha['total_atual'],
            'tipo' => 'estoque_baixo'
        ];
        $total_real_alertas++; 
    }
}

// 4. CRITICAL FIX: FATIA A LISTA DO POPUP EM EXATAMENTE 5 ITENS NO TOTAL GERAL
$alertas_exibidos = array_slice($alertas_completos, 0, 5);

// 5. RETORNA A CONTAGEM REAL E OS 5 ITENS
echo json_encode([
    'total' => $total_real_alertas, 
    'alertas' => $alertas_exibidos   
]);
exit;