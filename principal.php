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

    <!-- MENU LATERAL -->
    <aside class="sidebar">
        <h2 class="logo">INVEX</h2>

        <nav>
            <a href="principal.php" class="active">🏠 Home</a>
            <a href="cad_list_prods.php">📦 Produtos</a>
            <a href="#">📊 Relatórios</a>
            <a href="#">⚙️ Configurações</a>
        </nav>

        <a href="logout.php" class="logout">🚪 Sair</a>
    </aside>

    <!-- CONTEÚDO -->
    <main class="content">
        <header>
            <h1>Bem-vindo, <?php echo $_SESSION['nome']; ?> 👋</h1>
            <p>Gerencie seu sistema INVEX de forma simples e rápida.</p>
        </header>

        <section class="card">
            <h2>📌 Acesso rápido</h2>
            <p>Utilize o menu lateral para navegar pelas funções do sistema.</p>
        </section>

    </main>

</div>

</body>
</html>