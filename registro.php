<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $tipo = trim($_POST["tipo"]);

    if (!empty($nome) && !empty($tipo)) {
        $stmt = $conn->prepare("INSERT INTO funcionarios (NomeFuncionario, TipoCadastro) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $tipo);

        if ($stmt->execute()) {
            // Redireciona na hora com um parâmetro de sucesso na URL
            header("Location: login.html?status=sucesso");
            exit; 
        } else {
            // Em caso de erro, você pode voltar com um parâmetro de erro
            header("Location: registro.html?status=erro");
            exit;
        }
    }
}
?>