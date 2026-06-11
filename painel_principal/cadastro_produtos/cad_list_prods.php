<?php
require_once 'cad_list_prods_dados.php';
require_once 'cad_list_prods_listas.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"
href="cad_list_prods.css">
<link rel="icon" type="image/png" href="../../Imagens/Carrinho.png"width="70" height="70">
<title>Cadastro de Produtos</title>

<style>

.vermelho-validade{
    background-color: #ffb3b3;
}

.amarelo-validade{
    background-color: #fff0a6;
}

.paginacao a{
    padding: 6px 12px;
    background: #222;
    color: white;
    text-decoration: none;
    margin: 2px;
    border-radius: 5px;
}

.paginacao a:hover{
    background: #444;
}

</style>

</head>

<body>

 <!-- TOPO -->
 <header class="topbar">

<div class="top-left">

    <img src="../../imagens/carrinho2.png"
         width="70"
         height="70"
         alt="Logo Carrinho">

    <h1>INVEX</h1>

</div>

</header>
<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>Menu</h3>

        <nav>
            <a href="../painel_principal.php">🏠 Painel Principal</a></li>
            <a href="#">📦 Produtos</a>
            <a href="#">📋 Lotes</a>
            <a href="#">📊 Relatórios</a>
        </nav>
        <a href="../index.html"
               class="logout">

                🚪 Sair

            </a>
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="main-content">

        <div class="container">

            <h2>Sistema de Estoque</h2>

            <p class="usuario">
                Usuário logado:
                <b><?= htmlspecialchars($_SESSION['nome']) ?></b>
            </p>

            <div class="forms-grid">

                <!-- ==========================================
                     CADASTRO DE PRODUTOS
                =========================================== -->
                <div class="form-card">

                    <h3>Cadastro de itens</h3>

                    <form method="POST" action="">

                        <label>Nome do produto</label>
                        <input type="text" name="nome_produto" required>

                        <label>Marca</label>
                        <input type="text" name="marca" required>

                        <label>Descrição</label>
                        <textarea name="descricao"></textarea>

                        <input type="submit"
                               name="cadastrar_produto"
                               value="Cadastrar produto">

                    </form>

                </div>

                <!-- ==========================================
                     CADASTRO DE LOTES
                =========================================== -->
                <div class="form-card">

                    <h3>Cadastrar lote</h3>

                    <form method="POST" action="">

                        <label>ID do produto</label>
                        <input type="number" name="idproduto" required>

                        <label>Quantidade</label>
                        <input type="number" name="quantidade" required>

                        <label>Validade</label>
                        <input type="date" name="validade" required>

                        <input type="submit"
                               name="cadastrar_lote"
                               value="Cadastrar lote">

                    </form>

                </div>

            </div>

            <!-- ==========================================
                 LISTAGENS
            =========================================== -->

            <div class="lista-card">

                <h3>Listas do sistema</h3>

                <button type="button" onclick="mostrarListaProdutos()">
                    Mostrar / Ocultar Produtos
                </button>

                <button type="button" onclick="mostrarListaLotes()">
                    Mostrar / Ocultar Lotes
                </button>

                <br><br>

                <?= $htmlListaProdutos ?>
                <?= $htmlListaLotes ?>
                
            </div>

            <br>

            <a href="../painel_principal.php">
                Voltar ao painel
            </a>

        </div>

    </div>

</div>

<script src="cad_list_prods.js"></script>

</body>