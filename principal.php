<?php 
include 'verifica_login.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="principal.css">
<title>INVEX - Dashboard</title>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <h2 class="logo">INVEX</h2>

        <nav>
            <a class="active">🏠 Home</a>
            <a href="cad_list_prods.php">📦 Produtos</a>
            <a>📊 Relatórios</a>
            <a>⚙️ Configurações</a>
        </nav>

        <div class="bottom">
            <button onclick="toggleTheme()">🌙 Tema</button>
            <a href="logout.php">🚪 Sair</a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <button onclick="toggleMenu()">☰</button>

            <div class="user">
                <span><?php echo $_SESSION['nome']; ?></span>
            </div>
        </header>

        <!-- DASHBOARD -->
        <section class="dashboard">

            <div class="cards">
                <div class="card">
                    <h4>Produtos</h4>
                    <p id="prod">0</p>
                </div>

                <div class="card">
                    <h4>Entradas</h4>
                    <p id="ent">0</p>
                </div>

                <div class="card">
                    <h4>Saídas</h4>
                    <p id="sai">0</p>
                </div>
            </div>

            <!-- GRÁFICO -->
            <div class="chart-box">
                <h3>Movimentação</h3>
                <canvas id="grafico"></canvas>
            </div>

        </section>

    </main>

</div>

<script src="script.js"></script>
</body>
</html>