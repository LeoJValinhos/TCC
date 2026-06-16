<?php
/* =====================================================
DASHBOARD DE RELATÓRIOS (Herdando $conn e $idEmpresa)
===================================================== */

// TOTAL PRODUTOS
$sql_prod = "SELECT COUNT(*) AS total_produtos FROM produtos WHERE idEmpresa = $idEmpresa";
$total_produtos = $conn->query($sql_prod)->fetch_assoc()['total_produtos'];

// TOTAL LOTES
$sql_lotes = "SELECT COUNT(*) AS total_lotes FROM produtoslotes WHERE idEmpresa = $idEmpresa";
$total_lotes = $conn->query($sql_lotes)->fetch_assoc()['total_lotes'];

// VENCIDOS
$sql_vencidos = "SELECT COUNT(*) AS total_vencidos FROM produtoslotes WHERE idEmpresa = $idEmpresa AND status_lote = 'vencido'";
$total_vencidos = $conn->query($sql_vencidos)->fetch_assoc()['total_vencidos'];

// PROMOÇÕES
$sql_promo = "SELECT COUNT(*) AS total_promo FROM produtoslotes WHERE idEmpresa = $idEmpresa AND status_lote = 'promocao'";
$total_promo = $conn->query($sql_promo)->fetch_assoc()['total_promo'];

// ESTOQUE TOTAL
$sql_estoque = "SELECT SUM(quantidade) AS estoque_total FROM produtoslotes WHERE idEmpresa = $idEmpresa";
$res_estoque = $conn->query($sql_estoque)->fetch_assoc()['estoque_total'];
$estoque_total = $res_estoque ? $res_estoque : 0;
?>

<h2>Dashboard de Relatórios</h2>

<div class="dashboard-grid">

    <div class="card">
        <h3>Produtos Cadastrados</h3>
        <p id="prod"><?= $total_produtos ?></p> 
    </div>

    <div class="card">
        <h3>Total de Lotes</h3>
        <p id="lotes_cantina"><?= $total_lotes ?></p>
    </div>

    <div class="card" style="border-left: 5px solid red;">
        <h3>Lotes Vencidos</h3>
        <p id="vencidos"><?= $total_vencidos ?></p>
    </div>

    <div class="card" style="border-left: 5px solid gold;">
        <h3>Em Promoção</h3>
        <p id="promo"><?= $total_promo ?></p>
    </div>

    <div class="card">
        <h3>Estoque Físico Total</h3>
        <p id="estoque"><?= $estoque_total ?></p>
    </div>

</div>