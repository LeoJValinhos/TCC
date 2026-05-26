<?php

include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

/* =====================================================
FUNÇÃO FORMATAR DATA BR
===================================================== */

function formatarDataBR($data){

    if(empty($data)){
        return "-";
    }

    return date(
        "d/m/Y H:i",
        strtotime($data)
    );

}

/* =====================================================
CADASTRO DE PRODUTOS
===================================================== */

if(isset($_POST['cadastrar_produto'])){

    $nome_produto = trim($_POST["nome_produto"]);
    $marca = trim($_POST["marca"]);
    $descricao = trim($_POST["descricao"]);

    $criado_por_nome = $_SESSION['nome'];
    $criado_por_id = $_SESSION['idCadastro'];

    $data_criacao = date("Y-m-d H:i:s");

    if(!empty($nome_produto) && !empty($marca)){

        $verifica_produto = $conn->prepare("
        SELECT idProduto
        FROM produtos
        WHERE NomeProduto = ?
        AND MarcaProduto = ?
        ");

        $verifica_produto->bind_param(
        "ss",
        $nome_produto,
        $marca
        );

        $verifica_produto->execute();

        $resultado_verificacao =
        $verifica_produto->get_result();

        if($resultado_verificacao->num_rows > 0){

            echo "<script>
            alert('Esse produto já existe com essa marca');
            </script>";

        }else{

            $stmt = $conn->prepare("
            INSERT INTO produtos
            (
                NomeProduto,
                MarcaProduto,
                Descricao,
                criadopor_nome,
                criadoem,
                criadopor_id
            )
            VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
            "sssssi",
            $nome_produto,
            $marca,
            $descricao,
            $criado_por_nome,
            $data_criacao,
            $criado_por_id
            );

            if($stmt->execute()){

                echo "<script>
                alert('Produto cadastrado com sucesso');
                window.location.href='cad_list_prods.php';
                </script>";

            }else{

                echo "Erro ao cadastrar: " .
                $stmt->error;

            }

            exit;

        }

    }

}

/* =====================================================
CADASTRO DE LOTES
===================================================== */

if(isset($_POST['cadastrar_lote'])){

    $idproduto = trim($_POST['idproduto']);
    $quantidade = trim($_POST['quantidade']);
    $validade = trim($_POST['validade']);

    $criado_em = date("Y-m-d H:i:s");

    $verifica_produto = $conn->prepare("
    SELECT idProduto
    FROM produtos
    WHERE idProduto = ?
    ");

    $verifica_produto->bind_param(
    "i",
    $idproduto
    );

    $verifica_produto->execute();

    $resultado_produto =
    $verifica_produto->get_result();

    if($resultado_produto->num_rows > 0){

        $verifica_lote = $conn->prepare("
        SELECT idproduto
        FROM produtoslotes
        WHERE idproduto = ?
        AND validade = ?
        ");

        $verifica_lote->bind_param(
        "is",
        $idproduto,
        $validade
        );

        $verifica_lote->execute();

        $resultado_lote =
        $verifica_lote->get_result();

        if($resultado_lote->num_rows > 0){

            echo "<script>
            alert('Já existe um lote desse produto com essa validade');
            </script>";

        }else{

            $stmt_lote = $conn->prepare("
            INSERT INTO produtoslotes
            (
                idproduto,
                quantidade,
                validade,
                criado_em
            )
            VALUES (?, ?, ?, ?)
            ");

            $stmt_lote->bind_param(
            "iiss",
            $idproduto,
            $quantidade,
            $validade,
            $criado_em
            );

            if($stmt_lote->execute()){

                echo "<script>
                alert('Lote cadastrado com sucesso');
                window.location.href='cad_list_prods.php';
                </script>";

            }else{

                echo "Erro ao cadastrar lote: " .
                $stmt_lote->error;

            }

        }

    }else{

        echo "<script>
        alert('ID do produto não existe');
        </script>";

    }

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<link rel="stylesheet"
href="cad_list_prods.css">

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

<div class="container">

    <h2>Sistema de Estoque</h2>

    <p class="usuario">
        Usuário logado:
        <b><?php echo $_SESSION['nome']; ?></b>
    </p>

    <div class="forms-grid">

        <!-- =====================================================
        CADASTRO PRODUTOS
        ===================================================== -->

        <div class="form-card">

            <h3>Cadastro de itens</h3>

            <form method="POST">

                <label>Nome do produto</label>

                <input
                type="text"
                name="nome_produto"
                placeholder="Digite o nome do produto"
                required>

                <label>Marca</label>

                <input
                type="text"
                name="marca"
                placeholder="Digite a marca"
                required>

                <label>Descrição</label>

                <textarea
                name="descricao"
                placeholder="Descrição do produto"></textarea>

                <input
                type="submit"
                name="cadastrar_produto"
                value="Cadastrar produto">

            </form>

        </div>

        <!-- =====================================================
        CADASTRO LOTES
        ===================================================== -->

        <div class="form-card">

            <h3>Cadastrar lote</h3>

            <form method="POST">

                <label>ID do produto</label>

                <input
                type="number"
                name="idproduto"
                placeholder="Digite o ID"
                required>

                <label>Quantidade</label>

                <input
                type="number"
                name="quantidade"
                placeholder="Digite a quantidade"
                required>

                <label>Validade</label>

                <input
                type="date"
                name="validade"
                required>

                <input
                type="submit"
                name="cadastrar_lote"
                value="Cadastrar lote">

            </form>

        </div>

    </div>

    <div class="lista-card">

        <h3>Listas do sistema</h3>

        <button
        type="button"
        onclick="mostrarListaProdutos()">

        Mostrar / Ocultar Produtos

        </button>

        <button
        type="button"
        onclick="mostrarListaLotes()">

        Mostrar / Ocultar Lotes

        </button>

        <br><br>

        <!-- =====================================================
        LISTA PRODUTOS
        ===================================================== -->

        <div id="listaProdutos"
        style="<?php echo isset($_GET['pagina_produtos']) || isset($_GET['pesquisa']) || isset($_GET['ordenar']) ? 'display:block;' : 'display:none;'; ?>">

        <?php

        $pesquisa =
        isset($_GET['pesquisa'])
        ? trim($_GET['pesquisa'])
        : "";

        $ordenar =
        isset($_GET['ordenar'])
        ? $_GET['ordenar']
        : "id_desc";

        $limite = 5;

        $pagina =
        isset($_GET['pagina_produtos'])
        ? (int)$_GET['pagina_produtos']
        : 1;

        if($pagina < 1){
            $pagina = 1;
        }

        $inicio = ($pagina - 1) * $limite;

        $orderBy = "idProduto DESC";

        switch($ordenar){

            case "id_asc":
                $orderBy = "idProduto ASC";
            break;

            case "nome_asc":
                $orderBy = "NomeProduto ASC";
            break;

            case "nome_desc":
                $orderBy = "NomeProduto DESC";
            break;

            case "marca_asc":
                $orderBy = "MarcaProduto ASC";
            break;

            case "marca_desc":
                $orderBy = "MarcaProduto DESC";
            break;

        }

        $where = "";

        if(!empty($pesquisa)){

            $pesquisa_escape =
            $conn->real_escape_string($pesquisa);

            $where = "
            WHERE
            NomeProduto LIKE '%$pesquisa_escape%'
            OR
            MarcaProduto LIKE '%$pesquisa_escape%'
            ";
        }

        ?>

        <form method="GET" class="filtros-form">

            <input type="hidden" name="lista" value="produtos">

            <input
            type="text"
            name="pesquisa"
            placeholder="Pesquisar produto ou marca..."
            value="<?php echo htmlspecialchars($pesquisa); ?>">

            <select name="ordenar">

                <option value="id_desc"
                <?php if($ordenar == "id_desc") echo "selected"; ?>>
                ID Decrescente
                </option>

                <option value="id_asc"
                <?php if($ordenar == "id_asc") echo "selected"; ?>>
                ID Crescente
                </option>

                <option value="nome_asc"
                <?php if($ordenar == "nome_asc") echo "selected"; ?>>
                Nome A-Z
                </option>

                <option value="nome_desc"
                <?php if($ordenar == "nome_desc") echo "selected"; ?>>
                Nome Z-A
                </option>

                <option value="marca_asc"
                <?php if($ordenar == "marca_asc") echo "selected"; ?>>
                Marca A-Z
                </option>

                <option value="marca_desc"
                <?php if($ordenar == "marca_desc") echo "selected"; ?>>
                Marca Z-A
                </option>

            </select>

            <button type="submit">
                Filtrar
            </button>

        </form>

        <?php

        $sql_total = "
        SELECT COUNT(*) AS total
        FROM produtos
        $where
        ";

        $resultado_total =
        $conn->query($sql_total);

        $total_produtos =
        $resultado_total->fetch_assoc()['total'];

        $total_paginas =
        ceil($total_produtos / $limite);

        $sql_lista = "
        SELECT *
        FROM produtos

        $where

        ORDER BY $orderBy

        LIMIT $inicio, $limite
        ";

        $resultado_lista =
        $conn->query($sql_lista);

        if($resultado_lista->num_rows > 0){

            echo "<table>";

            echo "
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Marca</th>
                <th>Descrição</th>
                <th>Criado por</th>
                <th>Data</th>
            </tr>
            ";

            while($produto =
            $resultado_lista->fetch_assoc()){

                echo "<tr>";

                echo "<td>" .
                $produto['idProduto'] .
                "</td>";

                echo "<td>" .
                $produto['NomeProduto'] .
                "</td>";

                echo "<td>" .
                $produto['MarcaProduto'] .
                "</td>";

                echo "<td>" .
                $produto['Descricao'] .
                "</td>";

                echo "<td>" .
                $produto['criadopor_nome'] .
                "</td>";

                echo "<td>" .
                formatarDataBR(
                $produto['criadoem']
                ) .
                "</td>";

                echo "</tr>";
            }

            echo "</table>";

            echo "<div class='paginacao'>";

            for($i = 1; $i <= $total_paginas; $i++){

                echo "
                <a href='?

                lista=produtos

                &pagina_produtos=$i

                &pesquisa=" . urlencode($pesquisa) . "

                &ordenar=$ordenar'>

                $i

                </a>
                ";
            }

            echo "</div>";

        }else{

            echo "<p>Nenhum produto encontrado.</p>";

        }

        ?>

        </div>

        <!-- =====================================================
        LISTA LOTES
        ===================================================== -->

        <div id="listaLotes"
        style="<?php echo isset($_GET['lista']) && $_GET['lista'] == 'lotes' ? 'display:block;' : 'display:none;'; ?>">

        <?php

        $pesquisa_lote =
        isset($_GET['pesquisa_lote'])
        ? trim($_GET['pesquisa_lote'])
        : "";

        $validade_filtro =
        isset($_GET['validade_filtro'])
        ? trim($_GET['validade_filtro'])
        : "";

        $pagina_lotes =
        isset($_GET['pagina_lotes'])
        ? (int)$_GET['pagina_lotes']
        : 1;

        if($pagina_lotes < 1){
            $pagina_lotes = 1;
        }

        $inicio_lotes =
        ($pagina_lotes - 1) * $limite;

        $where_lotes = "WHERE 1=1";

        if(!empty($pesquisa_lote)){

            $pesquisa_lote_escape =
            $conn->real_escape_string($pesquisa_lote);

            $where_lotes .= "
            AND (
                produtos.NomeProduto LIKE '%$pesquisa_lote_escape%'
                OR
                produtos.MarcaProduto LIKE '%$pesquisa_lote_escape%'
            )
            ";
        }

        if(!empty($validade_filtro)){

            $where_lotes .= "
            AND produtoslotes.validade = '$validade_filtro'
            ";
        }

        ?>

        <form method="GET" class="filtros-form">

            <input type="hidden" name="lista" value="lotes">

            <input
            type="text"
            name="pesquisa_lote"
            placeholder="Pesquisar lote..."
            value="<?php echo htmlspecialchars($pesquisa_lote); ?>">

            <input
            type="date"
            name="validade_filtro"
            value="<?php echo $validade_filtro; ?>">

            <button type="submit">
                Filtrar
            </button>

        </form>

        <?php

        $sql_total_lotes = "
        SELECT COUNT(*) AS total
        FROM produtoslotes

        INNER JOIN produtos
        ON produtos.idProduto = produtoslotes.idproduto

        $where_lotes
        ";

        $resultado_total_lotes =
        $conn->query($sql_total_lotes);

        $total_lotes =
        $resultado_total_lotes->fetch_assoc()['total'];

        $total_paginas_lotes =
        ceil($total_lotes / $limite);

        $sql_lotes = "
        SELECT
            produtos.NomeProduto,
            produtos.MarcaProduto,
            produtoslotes.quantidade,
            produtoslotes.validade

        FROM produtoslotes

        INNER JOIN produtos
        ON produtos.idProduto = produtoslotes.idproduto

        $where_lotes

        ORDER BY produtoslotes.idlote DESC

        LIMIT $inicio_lotes, $limite
        ";

        $resultado_lotes =
        $conn->query($sql_lotes);

        if($resultado_lotes->num_rows > 0){

            echo "<table>";

            echo "
            <tr>
                <th>Produto</th>
                <th>Marca</th>
                <th>Quantidade</th>
                <th>Validade</th>
            </tr>
            ";

            while($lote =
            $resultado_lotes->fetch_assoc()){

                $classe = "";

                $hoje = new DateTime();
                $data_validade =
                new DateTime($lote['validade']);

                $dias =
                $hoje->diff($data_validade)->days;

                if($data_validade >= $hoje){

                    if($dias <= 30){

                        $classe = "vermelho-validade";

                    }elseif($dias <= 60){

                        $classe = "amarelo-validade";

                    }

                }

                echo "<tr class='$classe'>";

                echo "<td>" .
                $lote['NomeProduto'] .
                "</td>";

                echo "<td>" .
                $lote['MarcaProduto'] .
                "</td>";

                echo "<td>" .
                $lote['quantidade'] .
                "</td>";

                echo "<td>" .
                date(
                "d/m/Y",
                strtotime($lote['validade'])
                ) .
                "</td>";

                echo "</tr>";
            }

            echo "</table>";

            echo "<div class='paginacao'>";

            for($i = 1; $i <= $total_paginas_lotes; $i++){

                echo "
                <a href='?

                lista=lotes

                &pagina_lotes=$i

                &pesquisa_lote=" . urlencode($pesquisa_lote) . "

                &validade_filtro=$validade_filtro'>

                $i

                </a>
                ";
            }

            echo "</div>";

        }else{

            echo "<p>Nenhum lote cadastrado.</p>";

        }

        ?>

        </div>

    </div>

    <a href="principal.php">
        Voltar ao painel
    </a>

</div>

<script>

/* =====================================================
CORRIGE BUG DE REINICIAR LISTA
===================================================== */

window.onload = function(){

    const params =
    new URLSearchParams(window.location.search);

    const lista = params.get("lista");

    if(lista == "produtos"){

        document.getElementById("listaProdutos")
        .style.display = "block";

    }

    if(lista == "lotes"){

        document.getElementById("listaLotes")
        .style.display = "block";

    }

}

/* =====================================================
MOSTRAR PRODUTOS
===================================================== */

function mostrarListaProdutos(){

    var lista =
    document.getElementById("listaProdutos");

    if(lista.style.display == "none"){

        lista.style.display = "block";

    }else{

        lista.style.display = "none";

    }

}

/* =====================================================
MOSTRAR LOTES
===================================================== */

function mostrarListaLotes(){

    var lista =
    document.getElementById("listaLotes");

    if(lista.style.display == "none"){

        lista.style.display = "block";

    }else{

        lista.style.display = "none";

    }

}

</script>

</body>
</html>