<?php

require_once("../../funcoes/conexao.php");

$sql = "
SELECT
    idItem,
    nomeProduto,
    marcaProduto,
    descricaoProduto,
    quantidade,
    imagemProduto,
    meta,
    quantidadeParticipantes,
    status
FROM loja_virtual
";

$resultado = $conn->query($sql);

$produtos = [];

while($linha = $resultado->fetch_assoc()){

    $produtos[] = $linha;

}
?>