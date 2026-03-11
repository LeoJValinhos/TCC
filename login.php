<?php
session_start();

include 'conexao.php';

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM cadastros WHERE email = '$email' AND senha = '$senha'";

$resultado = mysqli_query($conn, $sql);

if(mysqli_num_rows($resultado) > 0){

    $usuario = mysqli_fetch_assoc($resultado);

    $_SESSION['nome'] = $usuario['nome'];

    header("Location: principal.php");
    exit();

}else{

    echo "<script>
            alert('Email ou senha incorretos');
            window.location.href='login.html';
          </script>";

}
?>