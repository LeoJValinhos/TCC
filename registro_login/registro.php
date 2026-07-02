<?php
require_once "../funcoes/conexao.php";

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

    // Validação de Maioridade
    if (!empty($datanascimento)) {
        $nascimento = new DateTime($datanascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento)->y;

        if ($idade < 18) {
            exibirAlerta('warning', 'Atenção', 'Cadastro não permitido para menores de 18 anos.', 'registro.html');
            return; 
        }
    }

    $possui_empresa = trim($_POST["possui_empresa"]);
    if ($tipo == "FUNCIONÁRIO") { $possui_empresa = "sim"; }

    $codigo_empresa = isset($_POST["codigo_empresa"]) ? trim($_POST["codigo_empresa"]) : "";
    $codigo_adm = isset($_POST["codigo_adm"]) ? trim($_POST["codigo_adm"]) : ""; 
    $nome_empresa = isset($_POST["nome_empresa"]) ? trim($_POST["nome_empresa"]) : "";
    $cnpj = isset($_POST["cnpj"]) ? trim($_POST["cnpj"]) : "";
    $idEmpresa = null;

    // Funções de Geração de Código
    function gerarCodigo($conn, $campo) {
        do {
            $codigo = str_pad(rand(0, 9999999), 7, "0", STR_PAD_LEFT);
            $verifica = $conn->prepare("SELECT idEmpresa FROM empresa WHERE $campo = ?");
            $verifica->bind_param("s", $codigo);
            $verifica->execute();
            $resultado = $verifica->get_result();
        } while($resultado->num_rows > 0);
        return $codigo;
    }

    // =====================================================
    // LÓGICA DE EMPRESA
    // =====================================================
    if($possui_empresa == "sim"){
        $sql = ($tipo == "EMPRESA/ADM") 
            ? "SELECT idEmpresa FROM empresa WHERE codigoEmpresa = ? AND codigoADM = ?" 
            : "SELECT idEmpresa FROM empresa WHERE codigoEmpresa = ?";
            
        $busca_empresa = $conn->prepare($sql);
        
        if($tipo == "EMPRESA/ADM") {
            $busca_empresa->bind_param("ss", $codigo_empresa, $codigo_adm);
        } else {
            $busca_empresa->bind_param("s", $codigo_empresa);
        }

        $busca_empresa->execute();
        $resultado_empresa = $busca_empresa->get_result();

        if($resultado_empresa->num_rows > 0){
            $empresa = $resultado_empresa->fetch_assoc();
            $idEmpresa = $empresa['idEmpresa'];
        } else {
            exibirAlerta('error', 'Erro', 'Código da empresa ou ADM inválido.', 'registro.html');
            return;
        }
    } else {
        if ($tipo == "FUNCIONÁRIO") {
            exibirAlerta('error', 'Erro', 'Funcionários não possuem permissão para criar empresas.', 'registro.html');
            return;
        }

        $stmt_empresa = $conn->prepare("INSERT INTO empresa (nomeEmpresa, CNPJ, codigoEmpresa, codigoADM) VALUES (?, ?, ?, ?)");
        $codigoGerado = gerarCodigo($conn, 'codigoEmpresa');
        $codigoAdmGerado = gerarCodigo($conn, 'codigoADM');
        $stmt_empresa->bind_param("ssss", $nome_empresa, $cnpj, $codigoGerado, $codigoAdmGerado);

        if($stmt_empresa->execute()){
            $idEmpresa = $conn->insert_id;
        } else {
            exibirAlerta('error', 'Erro', 'Erro ao criar empresa.', 'registro.html');
            return;
        }
    }

    // =====================================================
    // CADASTRO USUÁRIO FINAL
    // =====================================================
    $stmt = $conn->prepare("INSERT INTO cadastros (nome, sobrenome, senha, email, datanasc, cpf, celular, idEmpresa, tipocadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssis", $nome, $sobrenome, $senha, $email, $datanascimento, $cpf, $celular, $idEmpresa, $tipo);

    if ($stmt->execute()) {
        $idUsuario = $conn->insert_id;

        if($tipo == "EMPRESA/ADM"){
            $update_empresa = $conn->prepare("UPDATE empresa SET idAdm = ?, nomeAdm = ? WHERE idEmpresa = ?");
            $update_empresa->bind_param("isi", $idUsuario, $nome, $idEmpresa);
            $update_empresa->execute();
        }
        
        exibirAlerta('success', 'Sucesso!', 'Cadastro realizado com sucesso!', 'login.html?status=sucesso');
    } else {
        exibirAlerta('error', 'Erro', 'Erro ao processar seu cadastro.', 'registro.html');
    }
}
?>