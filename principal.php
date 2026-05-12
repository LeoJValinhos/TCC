<?php 
include 'verifica_login.php';
include 'conexao.php';

// SOMA TODAS AS QUANTIDADES DOS LOTES
$sql_total = "SELECT SUM(quantidade) AS total_produtos FROM produtoslotes";

$resultado_total = $conn->query($sql_total);

$total_produtos = 0;

if($resultado_total->num_rows > 0){

    $dados = $resultado_total->fetch_assoc();

    $total_produtos = $dados['total_produtos'];

    // CASO ESTEJA NULL
    if($total_produtos == null){
        $total_produtos = 0;
    }

}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="principal.css">
<title>INVEX</title>
</head>

<body>

<!-- TOPO -->
<header class="topbar">
    <div class="top-left">
        <img src="carrinho2.png" width="70" height="70" alt="Logo Carrinho">
        <h1>INVEX</h1>
    </div>
</header>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <nav>
            <a class="active">🏠 Home</a>
            <a href="cad_list_prods.php">📦 Produtos</a>
            <a>📊 Relatórios</a>
            <a>⚙️ Configurações</a>
        </nav>

        <a href="home.html" class="logout">🚪 Sair</a>
    </aside>

    <!-- CONTEÚDO -->
    <main class="main">

        <div class="top">
            <h2>Bem-vindo, <?php echo $_SESSION['nome']; ?> 👋</h2>
        </div>

        <div class="cards">

            <div class="card">
                <span>Produtos</span>
                <h2 id="prod"><?php echo $total_produtos; ?></h2>
            </div>

            <div class="card">
                <span>Entradas</span>
                <h2 id="ent">0</h2>
            </div>

            <div class="card">
                <span>Saídas</span>
                <h2 id="sai">0</h2>
            </div>

        </div>

    </main>

</div>

<script src="script.js"></script>
</body>
</html>