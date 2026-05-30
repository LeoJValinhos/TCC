<?php 

include '../funcoes/verifica_login.php';
include '../funcoes/conexao.php';

/* =====================================================
VERIFICA SE EXISTE ID DA EMPRESA
===================================================== */

if(!isset($_SESSION['idEmpresa'])){

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
ON p.idProduto = pl.idproduto

WHERE p.idEmpresa = ?

";

$stmt_total = $conn->prepare($sql_total);

$stmt_total->bind_param(
"i",
$idEmpresa
);

$stmt_total->execute();

$resultado_total =
$stmt_total->get_result();

if($resultado_total->num_rows > 0){

    $dados =
    $resultado_total->fetch_assoc();

    if($dados['total_produtos'] != null){

        $total_produtos =
        $dados['total_produtos'];

    }

}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"
href="painel_principal.css">

<title>INVEX</title>

</head>

<body>

<!-- TOPO -->
<header class="topbar">

    <div class="top-left">

        <img
        src="../Imagens/carrinho2.png"
        width="70"
        height="70"
        alt="Logo Carrinho">

        <h1>INVEX</h1>

    </div>

</header>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <nav>

            <a class="active">
                🏠 Home
            </a>

            <a href="cadastro_produtos/cad_list_prods.php">
                📦 Produtos
            </a>

            <a>
                📊 Relatórios
            </a>

            <a>
                ⚙️ Configurações
            </a>

        </nav>

        <a href="../index.html" class="logout">
            🚪 Sair
        </a>

    </aside>

    <!-- CONTEÚDO -->
    <main class="main">

        <div class="top">

            <h2>
                Bem-vindo,
                <?php echo $_SESSION['nome']; ?> 👋
            </h2>

            <p class="subtitulo">
                Controle geral do estoque da empresa
            </p>

        </div>

        <!-- CARD ÚNICO -->
        <div class="cards">

            <div class="card principal">

                <span>Total em estoque</span>

                <h2 id="prod">
                    <?php echo $total_produtos; ?>
                </h2>

                <p>
                    Soma total de todos os lotes cadastrados
                </p>

            </div>

        </div>

    </main>

</div>

<script src="painel_principal.js"></script>

</body>
</html>