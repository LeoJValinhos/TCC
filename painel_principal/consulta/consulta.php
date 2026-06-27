<?php
require_once 'consulta_logica.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
    href="consulta.css">
    <link rel="icon" type="image/png" href="../../Imagens/Carrinho.png"width="70" height="70">
    <title>INVEX - Consulta</title>

</head>

<body>

<style>
        .vermelho-validade {
        background-color: #d03c3c;
        color: white;
        font-weight: bold;
        border-radius: 25px;
        text-align: center;
        }

        .amarelo-validade {
        background-color: #d2bb48;
        color: black;
        font-weight: bold;
        border-radius: 25px;
        text-align: center;
        }

        .paginacao a {
            padding: 6px 12px;
            background: #222;
            color: white;
            text-decoration: none;
            margin: 2px;
            border-radius: 5px;
        }

        .paginacao a:hover {
            background: #444;
        }
    </style>

    <!-- TOPO -->
    <header class="topbar">

        <div class="top-left">

            <img src="../../Imagens/carrinho2.png"
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

                <a href="../painel_principal.php">
                    🏠 Home
                </a>

                <a href="../cadastro_produtos/cad_list_prods.php">
                    ➡️​ Entrada
                </a>

                <a href="../saida/saida.php">
                    ⬅️​ Saida
                </a>

                <a href="consulta.php">
                    📦 Consulta
                </a>

                 <a href="../compras/compras.php">
                    🛒​ Compras
                </a>

                <a href="../relatorios/buscar_relatorio.php">
                    📊 Relatórios
                </a>

                <a href="../configuracoes/painel_principal_config.php">
                    ⚙️ Configurações
                </a>

            </nav>

            <a href="../index.html"
               class="logout">

                🚪 Sair

            </a>

        </aside>

            <main class="main">


    <div class="top">

        <h2>Consulta de produtos e lotes</h2>

        <p class="subtitulo">
            Consulte os produtos cadastrados e seus respectivos lotes.
        </p>

    </div>

                <!-- LISTAGENS -->
                <div class="lista-card">

                    <div class="area-listas">

                        <h1 class="titulo-lista">
                            Lista de produtos</h1>
                        <?= $htmlListaProdutos ?>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <h1 class="titulo-lista">
                            Lista de lotes</h1>
                        <?= $htmlListaLotes ?>


                    </div>

                </div>

</main>

</div>

            <script src="consulta.js"></script>

</body>

</html>