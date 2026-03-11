<?php
session_start();
if(!isset($_SESSION['nome'])){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Página Principal</title>
</head>

<body>

<h2>Seja bem-vindo, <?php echo $_SESSION['nome']; ?>!</h2>

<a href="cad_list_prods.php"> <p> cadastrar/lista de produtos </P> </a>

</body>
</html>