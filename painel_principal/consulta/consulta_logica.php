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
LISTA PRODUTOS
===================================================== */

ob_start();

$pesquisa =
isset($_GET['pesquisa'])
? trim($_GET['pesquisa'])
: "";

$ordenar =
isset($_GET['ordenar'])
? $_GET['ordenar']
: "id_desc";

$limite = 5;

$pagina =
isset($_GET['pagina_produtos'])
? (int)$_GET['pagina_produtos']
: 1;

if($pagina < 1){
    $pagina = 1;
}

$inicio = ($pagina - 1) * $limite;

$orderBy = "idProduto DESC";

switch($ordenar){

    case "id_asc":
        $orderBy = "idProduto ASC";
        break;

    case "nome_asc":
        $orderBy = "NomeProduto ASC";
        break;

    case "nome_desc":
        $orderBy = "NomeProduto DESC";
        break;

    case "marca_asc":
        $orderBy = "MarcaProduto ASC";
        break;

    case "marca_desc":
        $orderBy = "MarcaProduto DESC";
        break;
}

$where = "
WHERE idEmpresa = $idEmpresa
";

if(!empty($pesquisa)){

    $pesquisa_escape =
    $conn->real_escape_string($pesquisa);

    $where = "
    WHERE idEmpresa = $idEmpresa
    AND (
        NomeProduto LIKE '%$pesquisa_escape%'
        OR
        MarcaProduto LIKE '%$pesquisa_escape%'
    )
    ";
}

$sql_total = "
SELECT COUNT(*) AS total
FROM produtos
$where
";

$resultado_total =
$conn->query($sql_total);

$total_produtos =
$resultado_total->fetch_assoc()['total'];

$total_paginas =
ceil($total_produtos / $limite);

$sql_lista = "
SELECT *
FROM produtos

$where

ORDER BY $orderBy

LIMIT $inicio, $limite
";

$resultado_lista =
$conn->query($sql_lista);
?>

<div id="listaProdutos">

<form method="GET" action="#listaProdutos" class="filtros-form">

<input type="hidden" name="lista" value="produtos">

<input
type="text"
name="pesquisa"
placeholder="Pesquisar produto ou marca..."
value="<?php echo htmlspecialchars($pesquisa); ?>">

<select name="ordenar">

<option value="id_desc" <?php if($ordenar == "id_desc") echo "selected"; ?>>
Código decrescente
</option>

<option value="id_asc" <?php if($ordenar == "id_asc") echo "selected"; ?>>
Código crescente
</option>

<option value="nome_asc" <?php if($ordenar == "nome_asc") echo "selected"; ?>>
Nome A-Z
</option>

<option value="nome_desc" <?php if($ordenar == "nome_desc") echo "selected"; ?>>
Nome Z-A
</option>

<option value="marca_asc" <?php if($ordenar == "marca_asc") echo "selected"; ?>>
Marca A-Z
</option>

<option value="marca_desc" <?php if($ordenar == "marca_desc") echo "selected"; ?>>
Marca Z-A
</option>

</select>

<button type="submit">
Filtrar
</button>

</form>

<?php

if($resultado_lista->num_rows > 0){

    echo "<table>";

    echo "
    <tr>
        <th>Código do produto</th>
        <th>Produto</th>
        <th>Marca</th>
        <th>Descrição</th>
        <th>Criado por</th>
        <th>Data</th>
    </tr>
    ";

    while($produto = $resultado_lista->fetch_assoc()){

        echo "<tr>";

        echo "<td>".$produto['idProduto']."</td>";
        echo "<td>".$produto['NomeProduto']."</td>";
        echo "<td>".$produto['MarcaProduto']."</td>";
        echo "<td>".$produto['Descricao']."</td>";
        echo "<td>".$produto['criadopor_nome']."</td>";
        echo "<td>".formatarDataBR($produto['criadoem'])."</td>";

        echo "</tr>";
    }

    echo "</table>";

    echo "<div class='paginacao'>";

    for($i = 1; $i <= $total_paginas; $i++){

echo "
<a href='?lista=produtos&pagina_produtos=$i&pesquisa="
. urlencode($pesquisa) .
"&ordenar=$ordenar#listaProdutos'>
$i
</a>
";
    }

    echo "</div>";

}else{

    echo "<p>Nenhum produto encontrado.</p>";

}

?>

</div>

<?php

$htmlListaProdutos = ob_get_clean();

/* =====================================================
LISTA LOTES
===================================================== */

ob_start();

$pesquisa_lote =
isset($_GET['pesquisa_lote'])
? trim($_GET['pesquisa_lote'])
: "";

$ordenar_lotes =
isset($_GET['ordenar_lotes'])
? $_GET['ordenar_lotes']
: "validade_asc";

$pagina_lotes =
isset($_GET['pagina_lotes'])
? (int)$_GET['pagina_lotes']
: 1;

if($pagina_lotes < 1){
    $pagina_lotes = 1;
}

$inicio_lotes =
($pagina_lotes - 1) * $limite;

$where_lotes = "
WHERE produtos.idEmpresa = $idEmpresa
";

$dataHojeFiltro = date("Y-m-d");
$dataLimiteFiltro = date("Y-m-d", strtotime("+60 days"));   

if(!empty($pesquisa_lote)){

    $pesquisa_lote_escape =
    $conn->real_escape_string($pesquisa_lote);

    $where_lotes .= "
    AND (
        produtos.NomeProduto LIKE '%$pesquisa_lote_escape%'
        OR
        produtos.MarcaProduto LIKE '%$pesquisa_lote_escape%'
    )
    ";
}
$orderByLotes = "produtoslotes.validade ASC";

switch($ordenar_lotes){

    case "validade_asc":
        $orderByLotes = "produtoslotes.validade ASC";
        break;

    case "validade_desc":
        $orderByLotes = "produtoslotes.validade DESC";
        break;

    case "produto_asc":
        $orderByLotes = "produtos.NomeProduto ASC";
        break;

    case "produto_desc":
        $orderByLotes = "produtos.NomeProduto DESC";
        break;

    case "marca_asc":
        $orderByLotes = "produtos.MarcaProduto ASC";
        break;

    case "marca_desc":
        $orderByLotes = "produtos.MarcaProduto DESC";
        break;

    case "quantidade_asc":
        $orderByLotes = "produtoslotes.quantidade ASC";
        break;

    case "quantidade_desc":
        $orderByLotes = "produtoslotes.quantidade DESC";
        break;

    case "vencidos":

    $where_lotes .= "
    AND produtoslotes.validade < '$dataHojeFiltro'
    ";

    $orderByLotes = "produtoslotes.validade ASC";
    break;

case "proximo_vencimento":

    $where_lotes .= "
    AND produtoslotes.validade >= '$dataHojeFiltro'
    AND produtoslotes.validade <= '$dataLimiteFiltro'
    ";

    $orderByLotes = "produtoslotes.validade ASC";
    break;

}

$sql_total_lotes = "
SELECT COUNT(*) AS total
FROM produtoslotes
INNER JOIN produtos
ON produtos.idProduto = produtoslotes.idproduto
$where_lotes
";

$resultado_total_lotes =
$conn->query($sql_total_lotes);

$total_lotes =
$resultado_total_lotes->fetch_assoc()['total'];

$total_paginas_lotes =
ceil($total_lotes / $limite);

// ALTERAÇÃO ESSENCIAL: Inclusão de preco_venda e desconto no SELECT da query
$sql_lotes = "
SELECT
    produtos.NomeProduto,
    produtos.MarcaProduto,
    produtoslotes.quantidade,
    produtoslotes.validade,
    produtoslotes.preco_venda,
    produtoslotes.desconto,
    produtoslotes.criadopor_nome
FROM produtoslotes
INNER JOIN produtos
ON produtos.idProduto = produtoslotes.idproduto
$where_lotes
ORDER BY $orderByLotes
LIMIT $inicio_lotes, $limite
";

$resultado_lotes =
$conn->query($sql_lotes);
?>

<div id="listaLotes">

<form method="GET" action="#listaLotes" class="filtros-form">

<input type="hidden" name="lista" value="lotes">

<input
type="text"
name="pesquisa_lote"
placeholder="Pesquisar lote..."
value="<?php echo htmlspecialchars($pesquisa_lote); ?>">

<select name="ordenar_lotes">

<option value="produto_asc" <?php if($ordenar_lotes == "produto_asc") echo "selected"; ?>>
Produto A-Z
</option>

<option value="produto_desc" <?php if($ordenar_lotes == "produto_desc") echo "selected"; ?>>
Produto Z-A
</option>

<option value="marca_asc" <?php if($ordenar_lotes == "marca_asc") echo "selected"; ?>>
Marca A-Z
</option>

<option value="marca_desc" <?php if($ordenar_lotes == "marca_desc") echo "selected"; ?>>
Marca Z-A
</option>

<option value="quantidade_asc" <?php if($ordenar_lotes == "quantidade_asc") echo "selected"; ?>>
Quantidade crescente
</option>

<option value="quantidade_desc" <?php if($ordenar_lotes == "quantidade_desc") echo "selected"; ?>>
Quantidade decrescente
</option>

<option value="validade_asc" <?php if($ordenar_lotes == "validade_asc") echo "selected"; ?>>
Validade crescente
</option>

<option value="validade_desc" <?php if($ordenar_lotes == "validade_desc") echo "selected"; ?>>
Validade decrescente
</option>

<option value="vencidos" <?php if($ordenar_lotes == "vencidos") echo "selected"; ?>>
Somente vencidos
</option>

<option value="proximo_vencimento" <?php if($ordenar_lotes == "proximo_vencimento") echo "selected"; ?>>
Próximo ao vencimento (30 dias)
</option>

</select>

<button type="submit">
Filtrar
</button>

</form>

<?php

if($resultado_lotes->num_rows > 0){

    echo "<table>";

    // ALTERAÇÃO: Inclusão da nova coluna 'Preço (Líquido)' no cabeçalho
    echo "
    <tr>
        <th>Produto</th>
        <th>Marca</th>
        <th>Quantidade</th>
        <th>Criado por</th>
        <th>Validade</th>
        <th>Preço (Líquido)</th>
    </tr>
    ";

    while($lote = $resultado_lotes->fetch_assoc()){

        /* =====================================================
        CORES DE VALIDADE DOS LOTES
        ===================================================== */

        $classe = "";

        $hoje = new DateTime();

        $data_validade =
        new DateTime($lote['validade']);

        /* =====================================================
        LOTE VENCIDO
        ===================================================== */

        if($data_validade < $hoje){

            $classe = "vermelho-validade";

        }else{

            $dias =
            $hoje->diff($data_validade)->days;

            /* =====================================================
            VENCE EM ATÉ 30 DIAS
            ===================================================== */

            if($dias <= 0){

                $classe = "vermelho-validade";

            }

            /* =====================================================
            VENCE ENTRE 31 E 60 DIAS
            ===================================================== */

            elseif($dias <= 30){

                $classe = "amarelo-validade";
            }
        }

        /* =====================================================
        CÁLCULO DINÂMICO DO PREÇO COM DESCONTO
        ===================================================== */
        $preco_original = (float)$lote['preco_venda'];
        $porcentagem_desconto = (float)$lote['desconto'];

        if ($porcentagem_desconto > 0) {
            $preco_calculado = $preco_original - ($preco_original * ($porcentagem_desconto / 100));
            
            $txt_original = number_format($preco_original, 2, ',', '.');
            $txt_calculado = number_format($preco_calculado, 2, ',', '.');
            $txt_pct = number_format($porcentagem_desconto, 0);

            // Montagem visual estruturada do preço promocional
            $exibicao_preco = "<span style='text-decoration: line-through; color: #a0aab5; font-size: 11px; display: block;'>R$ $txt_original</span>";
            $exibicao_preco .= "<span style='color: #28a745; font-weight: bold; font-size: 13.5px;'>R$ $txt_calculado</span>";
            $exibicao_preco .= "<span style='background: rgba(40, 167, 69, 0.15); color: #28a745; padding: 1px 5px; border-radius: 4px; font-size: 10px; font-weight: bold; margin-left: 4px;'>$txt_pct% OFF</span>";
        } else {
            $txt_normal = number_format($preco_original, 2, ',', '.');
            $exibicao_preco = "<span style='font-weight: bold; color: #ffffff;'>R$ $txt_normal</span>";
        }

        echo "<tr>";

        echo "<td>".$lote['NomeProduto']."</td>";
        echo "<td>".$lote['MarcaProduto']."</td>";
        echo "<td>".$lote['quantidade']."</td>";
        echo "<td>".$lote['criadopor_nome']."</td>";
        echo "<td class='$classe'>".date("d/m/Y", strtotime($lote['validade']))."</td>";
        // ALTERAÇÃO: Adicionada a célula com o componente visual de preço calculado
        echo "<td>".$exibicao_preco."</td>";

        echo "</tr>";
    }

    echo "</table>";

    echo "<div class='paginacao'>";

    for($i = 1; $i <= $total_paginas_lotes; $i++){

echo "
<a href='?lista=lotes&pagina_lotes=$i&pesquisa_lote="
. urlencode($pesquisa_lote) .
"&ordenar_lotes=$ordenar_lotes#listaLotes'>
$i
</a>
";
    }

    echo "</div>";

}else{

    echo "<p>Nenhum lote cadastrado.</p>";

}

?>

</div>

<?php

$htmlListaLotes = ob_get_clean();

?>