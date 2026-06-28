<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$usuario = 'root'; $senha = 'usbw'; $banco = 'databasetcc';
$conexao = mysqli_connect('localhost', $usuario, $senha, $banco);
if (!$conexao) { $conexao = mysqli_connect('127.0.0.1', $usuario, $senha, $banco); }

$idEmpresa = isset($_SESSION['idEmpresa']) ? (int)$_SESSION['idEmpresa'] : 0;
if (!$conexao || $idEmpresa <= 0) { echo json_encode(['total' => 0, 'alertas' => []]); exit; }

// Buscar dias de antecedência configurados
$dias_limite = 30;
$query_config = mysqli_query($conexao, "SELECT dias_antecedencia_vencimento FROM configuracoes_alertas WHERE idEmpresa = $idEmpresa LIMIT 1");
if ($row_cfg = mysqli_fetch_assoc($query_config)) { $dias_limite = (int)$row_cfg['dias_antecedencia_vencimento']; }

$alertas_completos = [];

// ======================================
// 1. ALERTAS DE VENCIMENTO (Vencido vs Vencendo)
// ======================================
$query_vencimento = "
SELECT p.idProduto, p.NomeProduto, l.validade, l.quantidade, l.numero_lote
FROM produtoslotes l
INNER JOIN produtos p ON l.idproduto = p.idProduto
WHERE p.idEmpresa = $idEmpresa AND l.idEmpresa = $idEmpresa
  AND l.validade <= DATE_ADD(CURDATE(), INTERVAL $dias_limite DAY)
  AND NOT EXISTS (SELECT 1 FROM alertas_ocultos ao WHERE ao.idProduto = p.idProduto AND ao.idEmpresa = $idEmpresa AND ao.tipo_alerta = 'vencimento' AND (ao.numero_lote = l.numero_lote OR ao.numero_lote IS NULL OR ao.numero_lote = ''))
ORDER BY l.validade ASC";

$res_vencimento = mysqli_query($conexao, $query_vencimento);
if ($res_vencimento) {
    while ($linha = mysqli_fetch_assoc($res_vencimento)) {
        // Restaurada a distinção entre Vencido e Vencendo
        $tipo = (strtotime($linha['validade']) < strtotime(date('Y-m-d'))) ? 'vencido' : 'vencendo';

        $alertas_completos[] = [
            'idProduto'   => $linha['idProduto'],
            'NomeProduto' => $linha['NomeProduto'],
            'validade'    => $linha['validade'],
            'quantidade'  => $linha['quantidade'],
            'tipo'        => $tipo
        ];
    }
}

// ======================================
// 2. ALERTAS DE ESTOQUE (Esgotado vs Baixo)
// ======================================
$query_estoque = "
SELECT p.idProduto, p.NomeProduto, p.estoque_minimo,
       COALESCE(SUM(l.quantidade), 0) as total_atual
FROM produtos p
LEFT JOIN produtoslotes l ON p.idProduto = l.idproduto
WHERE p.idEmpresa = $idEmpresa
  AND NOT EXISTS (SELECT 1 FROM alertas_ocultos ao WHERE ao.idProduto = p.idProduto AND ao.idEmpresa = $idEmpresa AND ao.tipo_alerta = 'estoque')
GROUP BY p.idProduto, p.NomeProduto, p.estoque_minimo
HAVING total_atual <= p.estoque_minimo AND p.estoque_minimo > 0";

$res_estoque = mysqli_query($conexao, $query_estoque);
if ($res_estoque) {
    while ($linha = mysqli_fetch_assoc($res_estoque)) {
        $tipo = ($linha['total_atual'] == 0) ? 'esgotado' : 'estoque_baixo';
        $alertas_completos[] = [
            'idProduto'   => $linha['idProduto'],
            'NomeProduto' => $linha['NomeProduto'],
            'quantidade'  => $linha['total_atual'],
            'tipo'        => $tipo
        ];
    }
}

echo json_encode(['total' => count($alertas_completos), 'alertas' => $alertas_completos]);
?>