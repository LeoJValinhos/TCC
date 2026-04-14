<?php 
include 'verifica_login.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="principal.css">
<title>INVEX - Painel</title>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <h2 class="logo">INVEX</h2>

        <nav>
            <a href="principal.php" class="active">🏠 Home</a>
            <a href="cad_list_prods.php">📦 Produtos</a>
            <a href="#">📊 Relatórios</a>
            <a href="#">⚙️ Configurações</a>
        </nav>

        <button onclick="toggleTheme()" class="theme-btn">🌙 Tema</button>
        <a href="logout.php" class="logout">🚪 Sair</a>
    </aside>

    <!-- CONTEÚDO -->
    <main class="content">

        <!-- TOPO -->
        <header class="topbar">
            <button class="menu-btn" onclick="toggleMenu()">☰</button>

            <div>
                <h1>Bem-vindo, <?php echo $_SESSION['nome']; ?> 👋</h1>
                <p>Controle seu estoque com o INVEX</p>
            </div>
        </header>

        <!-- CARDS -->
        <section class="cards">
            <div class="card">
                <h3>📦 Produtos</h3>
                <p id="produtos">0</p>
            </div>

            <div class="card">
                <h3>📈 Entradas</h3>
                <p id="entradas">0</p>
            </div>

            <div class="card">
                <h3>📉 Saídas</h3>
                <p id="saidas">0</p>
            </div>
        </section>

        <!-- INFO -->
        <section class="info">
            <h2>📌 Sistema INVEX</h2>
            <p>Gerencie produtos, entradas e saídas de forma simples e eficiente.</p>
        </section>

    </main>

</div>

<script src="script.js"></script>
</body>
</html>