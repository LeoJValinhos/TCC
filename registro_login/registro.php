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
    // EMPRESA
    // =====================================================

    $possui_empresa = trim($_POST["possui_empresa"]);

    $codigo_empresa = isset($_POST["codigo_empresa"])
    ? trim($_POST["codigo_empresa"])
    : "";

    $nome_empresa = isset($_POST["nome_empresa"])
    ? trim($_POST["nome_empresa"])
    : "";

    $cnpj = isset($_POST["cnpj"])
    ? trim($_POST["cnpj"])
    : "";

    $idEmpresa = null;

    // =====================================================
    // FUNÇÃO GERAR CÓDIGO
    // =====================================================

    function gerarCodigoEmpresa($conn){

        do{

            $codigo = str_pad(
                rand(0, 9999999),
                7,
                "0",
                STR_PAD_LEFT
            );

            $verifica = $conn->prepare("
            SELECT idEmpresa
            FROM empresa
            WHERE codigoEmpresa = ?
            ");

            $verifica->bind_param(
                "s",
                $codigo
            );

            $verifica->execute();

            $resultado =
            $verifica->get_result();

        }while($resultado->num_rows > 0);

        return $codigo;

    }

    // =====================================================
    // ENTRAR EMPRESA EXISTENTE
    // =====================================================

    if($possui_empresa == "sim"){

        $busca_empresa = $conn->prepare("
        SELECT idEmpresa
        FROM empresa
        WHERE codigoEmpresa = ?
        ");

        $busca_empresa->bind_param(
            "s",
            $codigo_empresa
        );

        $busca_empresa->execute();

        $resultado_empresa =
        $busca_empresa->get_result();

        if($resultado_empresa->num_rows > 0){

            $empresa =
            $resultado_empresa->fetch_assoc();

            $idEmpresa =
            $empresa['idEmpresa'];

        }else{

            echo "<script>

            alert('Código da empresa inválido');

            window.location.href='registro.html';

            </script>";

            exit;

        }

    }

    // =====================================================
    // CRIAR EMPRESA NOVA
    // =====================================================

    else{

        $codigoGerado =
        gerarCodigoEmpresa($conn);

        $stmt_empresa = $conn->prepare("
        INSERT INTO empresa
        (
            nomeEmpresa,
            CNPJ,
            codigoEmpresa
        )
        VALUES (?, ?, ?)
        ");

        $stmt_empresa->bind_param(
            "sss",
            $nome_empresa,
            $cnpj,
            $codigoGerado
        );

        if($stmt_empresa->execute()){

            $idEmpresa =
            $conn->insert_id;

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
        !empty($nome) &&
        !empty($sobrenome) &&
        !empty($email) &&
        !empty($datanascimento) &&
        !empty($cpf) &&
        !empty($celular) &&
        !empty($tipo)
    ) {

        $stmt = $conn->prepare("
        INSERT INTO cadastros
        (
            nome,
            sobrenome,
            senha,
            email,
            datanasc,
            cpf,
            celular,
            idEmpresa,
            tipocadastro
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssis",
            $nome,
            $sobrenome,
            $senha,
            $email,
            $datanascimento,
            $cpf,
            $celular,
            $idEmpresa,
            $tipo
        );

        if ($stmt->execute()) {

            $idUsuario =
            $conn->insert_id;

            // =====================================================
            // SE FOR ADM ATUALIZA EMPRESA
            // =====================================================

            if($tipo == "EMPRESA/ADM"){

                $update_empresa =
                $conn->prepare("
                UPDATE empresa

                SET
                idAdm = ?,
                nomeAdm = ?

                WHERE idEmpresa = ?
                ");

                $update_empresa->bind_param(
                    "isi",
                    $idUsuario,
                    $nome,
                    $idEmpresa
                );

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