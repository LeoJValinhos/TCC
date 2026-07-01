<?php

require_once "../funcoes/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // =====================================================
    // DADOS USUÁRIO
    // =====================================================

    $nome = trim($_POST["nome"]);
    $sobrenome = trim($_POST["sobrenome"]);
    $senha = trim($_POST["senha"]);
    $email = trim($_POST["email"]);
    $datanascimento = trim($_POST["datanascimento"]);
    $cpf = trim($_POST["cpf"]);
    $celular = trim($_POST["celular"]);
    $tipo = trim($_POST["tipo"]);

    // =====================================================
    // VALIDAÇÃO DE MAIORIDADE (18 ANOS)
    // =====================================================
    if (!empty($datanascimento)) {
        $nascimento = new DateTime($datanascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento)->y; // Calcula a diferença em anos

        if ($idade < 18) {
            echo "<script>
            alert('Cadastro não permitido para menores de 18 anos.');
            window.location.href='registro.html';
            </script>";
            exit;
        }
    }

    // =====================================================
    // EMPRESA
    // =====================================================

    $possui_empresa = trim($_POST["possui_empresa"]);

    // Trava de segurança back-end: Funcionário não pode criar nova empresa
    if ($tipo == "FUNCIONÁRIO") {
        $possui_empresa = "sim";
    }

    $codigo_empresa = isset($_POST["codigo_empresa"]) ? trim($_POST["codigo_empresa"]) : "";
    $codigo_adm = isset($_POST["codigo_adm"]) ? trim($_POST["codigo_adm"]) : ""; 

    $nome_empresa = isset($_POST["nome_empresa"]) ? trim($_POST["nome_empresa"]) : "";
    $cnpj = isset($_POST["cnpj"]) ? trim($_POST["cnpj"]) : "";

    $idEmpresa = null;

    // =====================================================
    // FUNÇÃO GERAR CÓDIGO DA EMPRESA
    // =====================================================

    function gerarCodigoEmpresa($conn){
        do{
            $codigo = str_pad(rand(0, 9999999), 7, "0", STR_PAD_LEFT);

            $verifica = $conn->prepare("
            SELECT idEmpresa FROM empresa WHERE codigoEmpresa = ?
            ");
            $verifica->bind_param("s", $codigo);
            $verifica->execute();
            $resultado = $verifica->get_result();
        }while($resultado->num_rows > 0);

        return $codigo;
    }

    // =====================================================
    // FUNÇÃO GERAR CÓDIGO DO ADM
    // =====================================================

    function gerarCodigoAdm($conn){
        do{
            $codigo = str_pad(rand(0, 9999999), 7, "0", STR_PAD_LEFT);

            $verifica = $conn->prepare("
            SELECT idEmpresa FROM empresa WHERE codigoADM = ?
            ");
            $verifica->bind_param("s", $codigo);
            $verifica->execute();
            $resultado = $verifica->get_result();
        }while($resultado->num_rows > 0);

        return $codigo;
    }

    // =====================================================
    // ENTRAR EMPRESA EXISTENTE
    // =====================================================

    if($possui_empresa == "sim"){

        if($tipo == "EMPRESA/ADM") {
            $busca_empresa = $conn->prepare("
            SELECT idEmpresa FROM empresa WHERE codigoEmpresa = ? AND codigoADM = ?
            ");
            $busca_empresa->bind_param("ss", $codigo_empresa, $codigo_adm);
        } else {
            $busca_empresa = $conn->prepare("
            SELECT idEmpresa FROM empresa WHERE codigoEmpresa = ?
            ");
            $busca_empresa->bind_param("s", $codigo_empresa);
        }

        $busca_empresa->execute();
        $resultado_empresa = $busca_empresa->get_result();

        if($resultado_empresa->num_rows > 0){
            $empresa = $resultado_empresa->fetch_assoc();
            $idEmpresa = $empresa['idEmpresa'];
        }else{
            $erro_msg = ($tipo == "EMPRESA/ADM") 
                ? 'Código da empresa ou código de ADM inválido' 
                : 'Código da empresa inválido';

            echo "<script>
            alert('$erro_msg');
            window.location.href='registro.html';
            </script>";
            exit;
        }

    }

    // =====================================================
    // CRIAR EMPRESA NOVA
    // =====================================================

    else{

        if ($tipo == "FUNCIONÁRIO") {
            echo "<script>
            alert('Funcionários não possuem permissão para criar empresas.');
            window.location.href='registro.html';
            </script>";
            exit;
        }

        $codigoGerado = gerarCodigoEmpresa($conn);
        $codigoAdmGerado = gerarCodigoAdm($conn); 

        $stmt_empresa = $conn->prepare("
        INSERT INTO empresa (nomeEmpresa, CNPJ, codigoEmpresa, codigoADM)
        VALUES (?, ?, ?, ?)
        ");

        $stmt_empresa->bind_param("ssss", $nome_empresa, $cnpj, $codigoGerado, $codigoAdmGerado);

        if($stmt_empresa->execute()){
            $idEmpresa = $conn->insert_id;
        }else{
            echo "<script>
            alert('Erro ao criar empresa');
            window.location.href='registro.html';
            </script>";
            exit;
        }

    }

    // =====================================================
    // CADASTRO USUÁRIO
    // =====================================================

    if (
        !empty($nome) && !empty($sobrenome) && !empty($email) &&
        !empty($datanascimento) && !empty($cpf) && !empty($celular) && !empty($tipo)
    ) {

        $stmt = $conn->prepare("
        INSERT INTO cadastros (nome, sobrenome, senha, email, datanasc, cpf, celular, idEmpresa, tipocadastro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("sssssssis", $nome, $sobrenome, $senha, $email, $datanascimento, $cpf, $celular, $idEmpresa, $tipo);

        if ($stmt->execute()) {

            $idUsuario = $conn->insert_id;

            if($tipo == "EMPRESA/ADM"){
                $update_empresa = $conn->prepare("
                UPDATE empresa SET idAdm = ?, nomeAdm = ? WHERE idEmpresa = ?
                ");

                $update_empresa->bind_param("isi", $idUsuario, $nome, $idEmpresa);
                $update_empresa->execute();
            }

            echo "<script>
            alert('Cadastro realizado com sucesso');
            window.location.href='login.html?status=sucesso';
            </script>";
            exit;

        } else {
            echo "<script>
            alert('Erro ao cadastrar usuário');
            window.location.href='registro.html';
            </script>";
            exit;
        }
    }
}
?>