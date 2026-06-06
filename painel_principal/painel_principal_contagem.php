<?php

include '../funcoes/verifica_login.php';
include '../funcoes/conexao.php';

/* =====================================================
VERIFICA SE EXISTE ID DA EMPRESA
===================================================== */

if (!isset($_SESSION['idEmpresa'])) {

    echo "
    <script>
        alert('Empresa não encontrada na sessão');
        window.location.href='../registro_login/login.html';
    </script>
    ";

    exit();
}

$idEmpresa = $_SESSION['idEmpresa'];

/* =====================================================
TOTAL DE PRODUTOS DA EMPRESA
===================================================== */

$total_produtos = 0;

$sql_total = "
SELECT 
    SUM(pl.quantidade) AS total_produtos
FROM produtoslotes pl
INNER JOIN produtos p
    ON p.idProduto = pl.idProduto
WHERE p.idEmpresa = ?
";

$stmt_total = $conn->prepare($sql_total);

$stmt_total->bind_param(
    "i",
    $idEmpresa
);

$stmt_total->execute();

$resultado_total = $stmt_total->get_result();

if ($resultado_total->num_rows > 0) {

    $dados = $resultado_total->fetch_assoc();

    if ($dados['total_produtos'] != null) {

        $total_produtos = $dados['total_produtos'];
    }
}

$nomeUsuario = $_SESSION['nome'];

?>