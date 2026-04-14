<?php 
include 'verifica_login.php';
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

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h2>INVEX</h2>

        <nav>
            <a class="active">🏠 Home</a>
            <a href="cad_list_prods.php">📦 Produtos</a>
            <a>📊 Relatórios</a>
            <a>⚙️ Configurações</a>
        </nav>

        <a href="logout.php" class="logout">Sair</a>
    </aside>

    <!-- CONTEÚDO -->
    <main class="main">

        <div class="top">
            <h1>Bem-vindo, <?php echo $_SESSION['nome']; ?></h1>
        </div>

        <div class="cards">
            <div class="card">
                <span>Produtos</span>
                <h2 id="prod">0</h2>
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