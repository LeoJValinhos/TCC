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
    status,
    fornecedor,
    valor_unitario,
    valor_total,
    descontopor_quantidade_produto,
    quantidade_deproduto_minimo_desconto
FROM loja_virtual
";

$resultado = $conn->query($sql);

$produtos = [];

while($linha = $resultado->fetch_assoc()){
    $produtos[] = $linha;
}
?>