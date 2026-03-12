<?php
include 'verifica_login.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="principal.css">
<title>Página Principal</title>
</head>

<body>

<center class="container">

<h2>Seja bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>

<br>

<a href="cad_list_prods.php">
<p>Cadastrar / Lista de produtos</p>
</a>

<br>

<a href="logout.php">
<p>Sair do sistema</p>
</a>

</center>

</body>
</html>