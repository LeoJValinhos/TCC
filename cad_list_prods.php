<?php
include 'verifica_login.php';
include 'conexao.php';

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

        /* ==========================================
        VERIFICA DUPLICADO
        ========================================== */

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

            /* ==========================================
            CADASTRA PRODUTO
            ========================================== */

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

    /* ==========================================
    VERIFICA PRODUTO
    ========================================== */

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

        /* ==========================================
        VERIFICA LOTE DUPLICADO
        ========================================== */

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

            /* ==========================================
            CADASTRA LOTE
            ========================================== */

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

</head>

<body>

<div class="container">

    <h2>Sistema de Estoque</h2>

    <p class="usuario">
        Usuário logado:
        <b><?php echo $_SESSION['nome']; ?></b>
    </p>

    <!-- =====================================================
    FORMULÁRIOS
    ===================================================== -->

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

    <!-- =====================================================
    LISTAS
    ===================================================== -->

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

        /* =====================================================
        FILTROS
        ===================================================== */

        $pesquisa =
        isset($_GET['pesquisa'])
        ? trim($_GET['pesquisa'])
        : "";

        $ordenar =
        isset($_GET['ordenar'])
        ? $_GET['ordenar']
        : "id_desc";

        /* =====================================================
        PAGINAÇÃO
        ===================================================== */

        $limite = 5;

        $pagina =
        isset($_GET['pagina_produtos'])
        ? (int)$_GET['pagina_produtos']
        : 1;

        if($pagina < 1){
            $pagina = 1;
        }

        $inicio = ($pagina - 1) * $limite;

        /* =====================================================
        ORDER BY
        ===================================================== */

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

        /* =====================================================
        PESQUISA
        ===================================================== */

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

        <!-- =====================================================
        FILTROS HTML
        ===================================================== -->

        <form method="GET" class="filtros-form">

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

        /* =====================================================
        TOTAL
        ===================================================== */

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

        /* =====================================================
        LISTA PRODUTOS
        ===================================================== */

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

            /* =====================================================
            PAGINAÇÃO
            ===================================================== */

            echo "<div class='paginacao'>";

            for($i = 1; $i <= $total_paginas; $i++){

                echo "
                <a href='?

                pagina_produtos=$i

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
        style="display:none;">

        <?php

        $sql_lotes = "
        SELECT
            produtos.NomeProduto,
            produtos.MarcaProduto,
            produtoslotes.quantidade,
            produtoslotes.validade

        FROM produtoslotes

        INNER JOIN produtos
        ON produtos.idProduto = produtoslotes.idproduto

        ORDER BY produtoslotes.idlote DESC

        LIMIT 5
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

                echo "<tr>";

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