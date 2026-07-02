<?php
session_start();
include '../funcoes/conexao.php';

// Função auxiliar para exibir alerta
function exibirAlerta($icon, $title, $text, $redirect) {
    echo "
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5/dark.min.css'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                background: '#1f1f1f',
                color: '#ffffff',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = '$redirect';
            });
        });
    </script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("
        SELECT
            c.*,
            e.codigoEmpresa
        FROM cadastros c
        INNER JOIN empresa e
            ON c.idEmpresa = e.idEmpresa
        WHERE c.email = ?
        AND c.senha = ?
    ");

    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        $_SESSION = array();
        $_SESSION['idCadastro'] = $usuario['idCadastro'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['idEmpresa'] = $usuario['idEmpresa'];
        $_SESSION['tipoCadastro'] = $usuario['tipocadastro'];
        $_SESSION['codigoEmpresa'] = $usuario['codigoEmpresa'];

        header("Location: ../painel_principal/painel_principal.php");
        exit(); 
    } else {
        // Alerta de falha sem travar o carregamento da página
        exibirAlerta('error', 'Login Inválido', 'E-mail ou senha incorretos.', 'login.html');
    }
}
?>