<?php
include 'verifica_login.php';
include 'conexao.php';

date_default_timezone_set('America/Sao_Paulo');

/* =========================
CADASTRO DE PRODUTOS
========================= */

if(isset($_POST['cadastrar_produto'])){

$nome_produto  = trim($_POST["nome_produto"]); 
$marca  = trim($_POST["marca"]); 
$descricao  = trim($_POST["descricao"]); 

$criado_por_nome = $_SESSION['nome'];
$criado_por_id = $_SESSION['idCadastro'];
$data_criacao = date("Y-m-d H:i:s");

if (!empty($nome_produto)) {

$stmt = $conn->prepare("INSERT INTO produtos 
(NomeProduto, MarcaProduto, Descricao, criadopor_nome, criadoem, criadopor_id) 
VALUES (?, ?, ?, ?, ?, ?)");

if(!$stmt){
    die("Erro no SQL: " . $conn->error);
}

$stmt->bind_param(
"sssssi",
$nome_produto,
$marca,
$descricao,
$criado_por_nome,
$data_criacao,
$criado_por_id
);

if ($stmt->execute()) {

echo "<script>
alert('Produto cadastrado com sucesso');
window.location.href='cad_list_prods.php';
</script>";

} else {

echo "Erro ao cadastrar: " . $stmt->error;

}

exit;

}
}

/* =========================
CADASTRO DE LOTES
========================= */

if(isset($_POST['cadastrar_lote'])){

    $idproduto = trim($_POST['idproduto']);
    $quantidade = trim($_POST['quantidade']);
    $validade = trim($_POST['validade']);
    $criado_em = date("Y-m-d H:i:s");

    // VERIFICA SE O PRODUTO EXISTE
    $verifica_produto = $conn->prepare("SELECT idProduto FROM produtos WHERE idProduto = ?");

    $verifica_produto->bind_param("i", $idproduto);

    $verifica_produto->execute();

    $resultado_produto = $verifica_produto->get_result();

    if($resultado_produto->num_rows > 0){

        // VERIFICA SE JÁ EXISTE LOTE COM MESMA VALIDADE
        $verifica_lote = $conn->prepare("
        SELECT idproduto 
        FROM produtoslotes 
        WHERE idproduto = ? AND validade = ?
        ");

        $verifica_lote->bind_param(
        "is",
        $idproduto,
        $validade
        );

        $verifica_lote->execute();

        $resultado_lote = $verifica_lote->get_result();

        if($resultado_lote->num_rows > 0){

            echo "<script>
            alert('Já existe um lote desse produto com essa validade');
            </script>";

        }else{

            // CADASTRA O LOTE
            $stmt_lote = $conn->prepare("INSERT INTO produtoslotes 
            (idproduto, quantidade, validade, criado_em)
            VALUES (?, ?, ?, ?)");

            if(!$stmt_lote){
                die("Erro no SQL do lote: " . $conn->error);
            }

            $stmt_lote->bind_param(
            "isss",
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

                echo "Erro ao cadastrar lote: " . $stmt_lote->error;

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="cad_list_prods.css">
<title>Cadastro de Produtos</title>
</head>

<body>

<center class="container">

<h2>Cadastro de itens</h2>

<p>Usuário logado: <b><?php echo $_SESSION['nome']; ?></b></p>

<form method="POST">

<p>Nome do produto</p>
<input type="text" name="nome_produto" required><br>

<p>Marca</p>
<input type="text" name="marca" required><br>

<p>Descrição</p>
<textarea name="descricao"></textarea><br>

<br>

<input type="submit" name="cadastrar_produto" value="Cadastrar produto">

</form>

<br>

<h2>Lista de produtos criados</h2>

<button type="button" onclick="mostrarLista()">
Mostrar / Ocultar Produtos
</button>

<br><br>

<div id="listaProdutos" style="display:none;">

<?php

$sql_lista = "SELECT * FROM produtos ORDER BY idProduto DESC";
$resultado_lista = $conn->query($sql_lista);

if($resultado_lista->num_rows > 0){

    echo "<table border='1' cellpadding='10'>";

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

<h2>Cadastrar lote do produto</h2>

<form method="POST">

<p>ID do produto</p>
<input type="number" name="idproduto" required><br>

<p>Quantidade</p>
<input type="number" name="quantidade" required><br>

<p>Validade</p>
<input type="date" name="validade" required><br>

<br>

<input type="submit" name="cadastrar_lote" value="Cadastrar lote">

</form>

<br><br>

<a href="principal.php">Voltar ao painel</a>

</center>

</body>
</html>