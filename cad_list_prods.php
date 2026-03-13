<?php
include 'verifica_login.php';
include 'conexao.php';

date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$nome_produto  = trim($_POST["nome_produto"]); 
$marca  = trim($_POST["marca"]); 
$descricao  = trim($_POST["descricao"]); 

$criado_por_nome = $_SESSION['nome'];
$criado_por_id = $_SESSION['id_usuario'];
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
?>

<!DOCTYPE html>
<html lang="en">

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

<input type="submit" value="Cadastrar produto">

</form>

<br>

<a href="principal.php">Voltar ao painel</a>

</center>

</body>
</html> 