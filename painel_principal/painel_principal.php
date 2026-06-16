<?php
require_once 'painel_principal_contagem.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
    href="painel_principal.css">
    <link rel="icon" type="image/png" href="../Imagens/Carrinho.png"width="70" height="70">
    <title>INVEX</title>

</head>

<body>

    <!-- TOPO -->
    <header class="topbar">

        <div class="top-left">

            <img src="../Imagens/carrinho2.png"
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

                <a href="painel_principal.php">
                    🏠 Home
                </a>

                <a href="cadastro_produtos/cad_list_prods.php">
                    📦 Produtos
                </a>

                 <a href="compras/compras.php">
                    🛒​ Compras
                </a>

                <a href="relatorios/buscar_relatorio.php">
                    📊 Relatórios
                </a>

                <a>
                    ⚙️ Configurações
                </a>

            </nav>

            <a href="../index.html"
               class="logout">

                🚪 Sair

            </a>

        </aside>

        <!-- CONTEÚDO -->
        <main class="main">

            <div class="top">

                <h2>
                    Bem-vindo,
                    <?php echo $nomeUsuario; ?> 👋
                </h2>

                <p class="subtitulo">
                    Controle geral do estoque da empresa
                </p>

            </div>

            <!-- CARD ÚNICO -->
            <div class="cards">

                <div class="card principal">

                    <span>
                        Total em estoque
                    </span>

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