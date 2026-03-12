<?php
session_start();
include 'conexao.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

$email = $_POST['email'];
$senha = $_POST['senha'];

$stmt = $conn->prepare("SELECT * FROM cadastros WHERE email = ? AND senha = ?");
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows > 0){

    $usuario = $resultado->fetch_assoc();

    $_SESSION['id_usuario'] = $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];

    header("Location: principal.php");
    exit();

}else{

    echo "<script>
    alert('Email ou senha incorretos');
    window.location.href='login.html';
    </script>";

}

}
?>