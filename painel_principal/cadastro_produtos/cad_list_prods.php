<body>

<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>Menu</h3>

        <ul>
            <li><a href="../painel_principal.php">🏠 Painel Principal</a></li>
            <li><a href="#">📦 Produtos</a></li>
            <li><a href="#">📋 Lotes</a></li>
            <li><a href="#">📊 Relatórios</a></li>
            <li><a href="../logout.php">🚪 Sair</a></li>
        </ul>
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