<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$nome = trim($_POST["nome"]);
$tipo = trim($_POST["tipo"]);

if (!empty($nome) && !empty($tipo)) {
        $stmt = $conn->prepare("INSERT INTO funcionarios (NomeFuncionario, TipoCadastro) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $tipo);

        if ($stmt->execute()) {
            echo "Cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Preencha todos os campos.";
    }
}

$conn->close(); 
?>