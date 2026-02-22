<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $sobrenome = trim($_POST["sobrenome"]);
    $email = trim($_POST["email"]);
    $datanascimento = trim($_POST["datanascimento"]);
    $cpf = trim($_POST["cpf"]);
    $celular = trim($_POST["celular"]);
    $tipo = trim($_POST["tipo"]);

    if (!empty($nome) && !empty($sobrenome)  && !empty($email) && !empty($datanascimento)  && !empty($cpf)  && !empty($celular) && !empty($tipo)) {
        $stmt = $conn->prepare("INSERT INTO cadastros (nome,sobrenome,email,datanasc,cpf,celular,tipoCadastro) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nome, $sobrenome, $email, $datanascimento, $cpf, $celular, $tipo);

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