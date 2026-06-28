
<?php

include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

/* =====================================================
FUNÇÃO FORMATAR DATA BR
===================================================== */

function formatarDataBR($data)
{
    if (empty($data)) {
        return "-";
    }

    return date(
        "d/m/Y H:i",
        strtotime($data)
    );
}

/* =====================================================
DADOS USUÁRIO LOGADO
===================================================== */

$idEmpresa = $_SESSION['idEmpresa'];
$criado_por_nome = $_SESSION['nome'];
$criado_por_id = $_SESSION['idCadastro'];

/* =====================================================
CADASTRO DE PRODUTOS
===================================================== */

if (isset($_POST['cadastrar_produto'])) {

    $nome_produto = trim($_POST["nome_produto"]);
    $marca = trim($_POST["marca"]);
    $descricao = trim($_POST["descricao"]);

   /* =====================================================
    NOVOS CAMPOS
    ===================================================== */

    $preco_padrao_compra =
    (float) $_POST["preco_padrao_compra"];

    $preco_padrao_venda =
    (float) $_POST["preco_padrao_venda"];

    $estoque_minimo =
    (int) $_POST["estoque_minimo"];

    $data_criacao = date("Y-m-d H:i:s");

    if (!empty($nome_produto) && !empty($marca)) {

        $verifica_produto = $conn->prepare("
            SELECT idProduto
            FROM produtos
            WHERE NomeProduto = ?
            AND MarcaProduto = ?
            AND idEmpresa = ?
        ");

        $verifica_produto->bind_param(
            "ssi",
            $nome_produto,
            $marca,
            $idEmpresa
        );

        $verifica_produto->execute();

        $resultado_verificacao =
        $verifica_produto->get_result();

        if ($resultado_verificacao->num_rows > 0) {

            echo "<script>
                alert('Esse produto já existe nessa empresa');
            </script>";

        } else {

            $stmt = $conn->prepare("
                INSERT INTO produtos
                (
                   NomeProduto,
                   MarcaProduto,
                   Descricao,
                   preco_padrao_compra,
                   preco_padrao_venda,
                   estoque_minimo,
                   idEmpresa,
                   criadopor_nome,
                   criadoem,
                   criadopor_id
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
               "sssddiissi",
               $nome_produto,
               $marca,
               $descricao,
               $preco_padrao_compra,
               $preco_padrao_venda,
               $estoque_minimo,
               $idEmpresa,
               $criado_por_nome,
               $data_criacao,
               $criado_por_id
            );

            if ($stmt->execute()) {

                echo "<script>
                    alert('Produto cadastrado com sucesso');
                    window.location.href='cad_list_prods.php';
                </script>";

            } else {

                echo "Erro ao cadastrar: " .
                $stmt->error;

            }

            exit;
        }
    }
}

/* =====================================================
CADASTRO DE LOTES
===================================================== */

if(isset($_POST['cadastrar_lote'])){

    $idproduto = trim($_POST['idproduto']);

    $quantidade = trim($_POST['quantidade']);

    $validade = trim($_POST['validade']);
    /* =====================================================
     NOVOS CAMPOS DO LOTE
    ===================================================== */

    $numero_lote =
    "LOTE-" . $idproduto;

    $preco_compra =
    (float) $_POST['preco_compra'];

    $preco_venda =
    (float) $_POST['preco_venda'];

    $desconto =
    (float) $_POST['desconto'];

    /* =====================================================
    STATUS DO LOTE
    ===================================================== */

    $status_lote = "normal";

if($desconto > 0){

    $status_lote = "promocao";

}

if($validade < date("Y-m-d")){

    $status_lote = "vencido";

}

    $criado_em = date("Y-m-d H:i:s");

    $verifica_produto = $conn->prepare("
    SELECT idProduto
    FROM produtos
    WHERE idProduto = ?
    AND idEmpresa = ?
    ");

    $verifica_produto->bind_param(
    "ii",
    $idproduto,
    $idEmpresa
    );

    $verifica_produto->execute();

    $resultado_produto =
    $verifica_produto->get_result();

    if($resultado_produto->num_rows > 0){

        $verifica_lote = $conn->prepare("
        SELECT idproduto
        FROM produtoslotes
        WHERE idproduto = ?
        AND numero_lote = ?
        ");

        $verifica_lote->bind_param(
        "is",
        $idproduto,
        $numero_lote
        );

        $verifica_lote->execute();

        $resultado_lote =
        $verifica_lote->get_result();

        if($resultado_lote->num_rows > 0){

            echo "<script>
            alert('Já existe um lote desse produto com esse número de lote');
            </script>";

        }else{

            $stmt_lote = $conn->prepare("
            INSERT INTO produtoslotes
          (
             idproduto,
             numero_lote,
             quantidade,
             validade,
             preco_compra,
             preco_venda,
             desconto,
             status_lote,
             criado_em,
             idEmpresa             
          )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt_lote->bind_param(
              "isisdddssi",
              $idproduto,
              $numero_lote,
              $quantidade,
              $validade,
              $preco_compra,
              $preco_venda,
              $desconto,
              $status_lote,
              $criado_em,
              $idEmpresa
            );

            if($stmt_lote->execute()){

                echo "<script>
                alert('Lote cadastrado com sucesso');
                window.location.href='cad_list_prods.php';
                </script>";

            }else{

                echo "Erro ao cadastrar lote: " .
                $stmt_lote->error;

            }

        }

    }else{

        echo "<script>
        alert('Produto não pertence à sua empresa');
        </script>";

    }

}

?>