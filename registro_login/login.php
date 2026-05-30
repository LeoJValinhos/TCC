<?php
session_start();

include '../funcoes/conexao.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("
        SELECT *
        FROM cadastros
        WHERE email = ?
        AND senha = ?
    ");

    $stmt->bind_param(
    "ss",
    $email,
    $senha
    );

    $stmt->execute();

    $resultado =
    $stmt->get_result();

    if($resultado->num_rows > 0){

        $usuario =
        $resultado->fetch_assoc();

        /* LIMPA SESSÃO ANTIGA */
        $_SESSION = array();

        /* SESSÕES */
        $_SESSION['idCadastro'] =
        $usuario['idCadastro'];

        $_SESSION['nome'] =
        $usuario['nome'];

        $_SESSION['idEmpresa'] =
        $usuario['idEmpresa'];

        $_SESSION['tipoCadastro'] =
        $usuario['tipocadastro'];

        header(
        "Location: ../painel_principal/painel_principal.php"
        );

        exit();

    }else{

        echo "
        <script>

        alert('Email ou senha incorretos');

        window.location.href='login.html';

        </script>
        ";

    }

}
?>