<?php

include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

$htmlListaProdutos = "";
$htmlListaLotes = "";

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

<div id="listaProdutos"
style="<?php echo isset($_GET['pagina_produtos']) || isset($_GET['pesquisa']) || isset($_GET['ordenar']) ? 'display:block;' : 'display:none;'; ?>">

<form method="GET" class="filtros-form">

<input type="hidden" name="lista" value="produtos">

<input
type="text"
name="pesquisa"
placeholder="Pesquisar produto ou marca..."
value="<?php echo htmlspecialchars($pesquisa); ?>">

<select name="ordenar">

<option value="id_desc" <?php if($ordenar == "id_desc") echo "selected"; ?>>
ID Decrescente
</option>

<option value="id_asc" <?php if($ordenar == "id_asc") echo "selected"; ?>>
ID Crescente
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
        <th>ID</th>
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
        "&ordenar=$ordenar'>
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

$validade_filtro =
isset($_GET['validade_filtro'])
? trim($_GET['validade_filtro'])
: "";

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

if(!empty($validade_filtro)){

    $where_lotes .= "
    AND produtoslotes.validade = '$validade_filtro'
    ";
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

$sql_lotes = "
SELECT
    produtos.NomeProduto,
    produtos.MarcaProduto,
    produtoslotes.quantidade,
    produtoslotes.validade
FROM produtoslotes
INNER JOIN produtos
ON produtos.idProduto = produtoslotes.idproduto
$where_lotes
ORDER BY produtoslotes.idlote DESC
LIMIT $inicio_lotes, $limite
";

$resultado_lotes =
$conn->query($sql_lotes);
?>

<div id="listaLotes"
style="<?php echo isset($_GET['lista']) && $_GET['lista'] == 'lotes' ? 'display:block;' : 'display:none;'; ?>">

<form method="GET" class="filtros-form">

<input type="hidden" name="lista" value="lotes">

<input
type="text"
name="pesquisa_lote"
placeholder="Pesquisar lote..."
value="<?php echo htmlspecialchars($pesquisa_lote); ?>">

<input
type="date"
name="validade_filtro"
value="<?php echo $validade_filtro; ?>">

<button type="submit">
Filtrar
</button>

</form>

<?php

if($resultado_lotes->num_rows > 0){

    echo "<table>";

    echo "
    <tr>
        <th>Produto</th>
        <th>Marca</th>
        <th>Quantidade</th>
        <th>Validade</th>
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

    if($dias <= 30){

        $classe = "vermelho-validade";

    }

    /* =====================================================
    VENCE ENTRE 31 E 60 DIAS
    ===================================================== */

    elseif($dias <= 60){

        $classe = "amarelo-validade";
            }
        }

        echo "<tr class='$classe'>";

        echo "<td>".$lote['NomeProduto']."</td>";
        echo "<td>".$lote['MarcaProduto']."</td>";
        echo "<td>".$lote['quantidade']."</td>";
        echo "<td>".date("d/m/Y", strtotime($lote['validade']))."</td>";

        echo "</tr>";
    }

    echo "</table>";

    echo "<div class='paginacao'>";

    for($i = 1; $i <= $total_paginas_lotes; $i++){

        echo "
        <a href='?lista=lotes&pagina_lotes=$i&pesquisa_lote="
        . urlencode($pesquisa_lote) .
        "&validade_filtro=$validade_filtro'>
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