<?php
date_default_timezone_set('America/Sao_Paulo');
if (!isset($_SESSION)) {
    session_start();
}

include_once __DIR__ . '/../../funcoes/verifica_login.php';
include_once __DIR__ . '/../../funcoes/conexao.php';

$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;

$alertas_vencidos = [];
$alertas_proximos = []; 
$alertas_estoque_baixo = []; // Estoque > 0 e <= minimo
$alertas_esgotados = [];     // Estoque == 0
$dias_antecedencia = 30; 

if ($idEmpresa) {
    $data_hoje = date('Y-m-d');

    // Busca configuração de dias
    try {
        $query_cfg = "SELECT dias_antecedencia_vencimento FROM configuracoes_alertas WHERE idEmpresa = $idEmpresa LIMIT 1";
        $res_cfg = $conn->query($query_cfg);
        if($res_cfg && $res_cfg->num_rows > 0){
            $row_cfg = $res_cfg->fetch_assoc();
            $dias_antecedencia = intval($row_cfg['dias_antecedencia_vencimento']);
        }
    } catch (Throwable $t) { $dias_antecedencia = 30; }
    
    $data_limite_vencimento = date('Y-m-d', strtotime("+$dias_antecedencia days"));

    /* =========================================================================
       PROCESSO 1: ALERTAS DE VENCIMENTO
       ========================================================================= */
    try {
        $query_lotes = "
            SELECT pl.*, p.NomeProduto, p.MarcaProduto 
            FROM produtoslotes pl
            JOIN produtos p ON pl.idproduto = p.idproduto
            WHERE pl.idEmpresa = $idEmpresa 
              AND pl.quantidade > 0
              AND NOT EXISTS (
                  SELECT 1 FROM alertas_ocultos ao 
                  WHERE ao.idProduto = pl.idProduto 
                    AND ao.numero_lote = pl.numero_lote 
                    AND ao.idEmpresa = $idEmpresa
                    AND ao.tipo_alerta = 'vencimento'
              )
        ";
        $res_lotes = $conn->query($query_lotes);
        if ($res_lotes) {
            while($lote = $res_lotes->fetch_assoc()) {
                if($lote['validade'] < $data_hoje) {
                    $alertas_vencidos[] = $lote;
                } elseif($lote['validade'] <= $data_limite_vencimento) {
                    $alertas_proximos[] = $lote; 
                }
            }
        }
    } catch (Throwable $t) {
        $alertas_vencidos = [];
        $alertas_proximos = [];
    }

    /* =========================================================================
   PROCESSO 2: ALERTA DE ESTOQUE (MÍNIMO E ESGOTADO)
   ========================================================================= */
try {
    $query_estoque = "
        SELECT p.idProduto, p.NomeProduto, p.MarcaProduto, p.estoque_minimo,
               IFNULL(SUM(pl.quantidade), 0) as total_estoque
        FROM produtos p
        LEFT JOIN produtoslotes pl ON p.idProduto = pl.idProduto
        WHERE p.idEmpresa = $idEmpresa
          AND NOT EXISTS (
              SELECT 1 FROM alertas_ocultos ao 
              WHERE ao.idProduto = p.idProduto 
                AND ao.idEmpresa = $idEmpresa
                AND ao.tipo_alerta = 'estoque'
          )
        GROUP BY p.idProduto
        HAVING total_estoque <= p.estoque_minimo
    ";
    
    $res_estoque = $conn->query($query_estoque);
    $alertas_estoque_baixo = []; // Limpa o array antes de preencher

    if ($res_estoque) {
        while($prod = $res_estoque->fetch_assoc()) {
            // O array agora recebe todos os produtos com problema (0 ou abaixo do mínimo)
            $alertas_estoque_baixo[] = $prod;
        }
    }
} catch (Throwable $t) {
    $alertas_estoque_baixo = [];
}
}
?>