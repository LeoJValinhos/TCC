<?php
require_once "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //variaveis e inputs
    $nome = trim($_POST["nome"]); 
    $sobrenome = trim($_POST["sobrenome"]);
    $senha = trim($_POST["senha"]);
    $email = trim($_POST["email"]);
    $datanascimento = trim($_POST["datanascimento"]);
    $cpf = trim($_POST["cpf"]);
    $celular = trim($_POST["celular"]);
    $tipo = trim($_POST["tipo"]);

    // aqui pra quem for ver ele verifica se os dados ta vazio e ja vai inserir todos, ali cada ssss e ??? é para cada dado inserido, se for adicionar mais dado futuramente lembrar de acresentar e autalizar. (qual quer coisa falar com leleco)
    
    if (!empty($nome) && !empty($sobrenome)  && !empty($email) && !empty($datanascimento)  && !empty($cpf)  && !empty($celular) && !empty($tipo)) {
        $stmt = $conn->prepare("INSERT INTO cadastros (nome,sobrenome,senha,email,datanasc,cpf,celular,tipoCadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $nome, $sobrenome, $senha, $email, $datanascimento, $cpf, $celular, $tipo);

        if ($stmt->execute()) {
            // Redireciona na hora com um parâmetro de sucesso na URL
            header("Location: login.html?status=sucesso");
            exit; 
        } else {
            // Em caso de erro, você pode voltar com um parâmetro de erro
             echo "<script>
            alert('Email ou senha incorretos');
            window.location.href='registro.html';
          </script>";

            exit;
        }
    }
}
?>