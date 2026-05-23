<body>

<div class="container">

    <h2>Sistema de Estoque</h2>

    <p class="usuario">
        Usuário logado: <b><?php echo $_SESSION['nome']; ?></b>
    </p>

    <!-- FORMULÁRIOS -->
    <div class="forms-grid">

        <!-- CADASTRO PRODUTO -->
        <div class="form-card">

            <h3>Cadastro de itens</h3>

            <form method="POST">

                <label>Nome do produto</label>
                <input type="text"
                name="nome_produto"
                placeholder="Digite o nome do produto"
                required>

                <label>Marca</label>
                <input type="text"
                name="marca"
                placeholder="Digite a marca do produto"
                required>

                <label>Descrição</label>

                <textarea
                name="descricao"
                placeholder="Descreva o produto..."></textarea>

                <input type="submit"
                name="cadastrar_produto"
                value="Cadastrar produto">

            </form>

        </div>

        <!-- CADASTRO LOTE -->
        <div class="form-card">

            <h3>Cadastrar lote do produto</h3>

            <form method="POST">

                <label>ID do produto</label>

                <input type="number"
                name="idproduto"
                placeholder="Digite o ID do produto"
                required>

                <label>Quantidade</label>

                <input type="number"
                name="quantidade"
                placeholder="Digite a quantidade"
                required>

                <label>Validade</label>

                <input type="date"
                name="validade"
                required>

                <input type="submit"
                name="cadastrar_lote"
                value="Cadastrar lote">

            </form>

        </div>

    </div>

    <!-- LISTA -->
    <div class="lista-card">

        <h3>Lista de produtos criados</h3>

        <button type="button" onclick="mostrarLista()">
            Mostrar / Ocultar Produtos
        </button>

        <br><br>

        <div id="listaProdutos" style="display:none;">

        <?php

        $sql_lista = "SELECT * FROM produtos ORDER BY idProduto DESC";
        $resultado_lista = $conn->query($sql_lista);

        if($resultado_lista->num_rows > 0){

            echo "<table>";

            echo "<tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Marca</th>
                    <th>Descrição</th>
                    <th>Criado por</th>
                    <th>Data</th>
                  </tr>";

            while($produto = $resultado_lista->fetch_assoc()){

                echo "<tr>";

                echo "<td>" . $produto['idProduto'] . "</td>";
                echo "<td>" . $produto['NomeProduto'] . "</td>";
                echo "<td>" . $produto['MarcaProduto'] . "</td>";
                echo "<td>" . $produto['Descricao'] . "</td>";
                echo "<td>" . $produto['criadopor_nome'] . "</td>";
                echo "<td>" . $produto['criadoem'] . "</td>";

                echo "</tr>";
            }

            echo "</table>";

        }else{

            echo "<p>Nenhum produto cadastrado.</p>";

        }

        ?>

        </div>

    </div>

    <a href="principal.php">Voltar ao painel</a>

</div>

<script>

function mostrarLista(){

    var lista = document.getElementById("listaProdutos");

    if(lista.style.display == "none"){

        lista.style.display = "block";

    }else{

        lista.style.display = "none";
    }

}

</script>

</body>