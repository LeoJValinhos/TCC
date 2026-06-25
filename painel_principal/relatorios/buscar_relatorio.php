<?php



ini_set('display_errors', 1);



ini_set('display_startup_errors', 1);



error_reporting(E_ALL);







date_default_timezone_set('America/Sao_Paulo');







include '../../funcoes/verifica_login.php';



include '../../funcoes/conexao.php';







if (!isset($_SESSION)) {



    session_start();



}







$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;



$nomeUsuario = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : "Usuário";







$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : "dashboard";



$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : "todos";







// MONTAGEM DO FILTRO DE DATA



// Importante: s.data_saida mapeia saídas, l.criado_em (ou similar) mapeia a criação de lotes se aplicável



// MONTAGEM DO FILTRO DE DATA PADRONIZADO COM O SEU BANCO (data_saida)



$sql_filtro_data = "";



if ($periodo == "hoje") {



    $sql_filtro_data = " AND DATE(s.data_saida) = CURDATE()";



} elseif ($periodo == "semana") {



    $sql_filtro_data = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";



} elseif ($periodo == "mes") {



    $sql_filtro_data = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";



}







// Compartilha o filtro globalmente para que os includes consigam ler



$GLOBALS['filtro_data'] = $sql_filtro_data;



$GLOBALS['periodo_atual'] = $periodo;







?>







<!DOCTYPE html>



<html lang="pt-br">



<head>



    <meta charset="UTF-8">



    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    <link rel="stylesheet" href="css/relatorios.css">



    <link rel="icon" type="image/png" href="../../Imagens/Carrinho.png" width="70" height="70">



    <title>INVEX - Relatórios</title>



</head>



<body>







    <header class="topbar">



        <div class="top-left">



            <img src="../../Imagens/carrinho2.png" width="70" height="70" alt="Logo Carrinho">



            <h1>INVEX</h1>



        </div>



    </header>







    <div class="layout">



        <aside class="sidebar">



            <nav>



                <a href="../painel_principal.php">🏠 Home</a>



                <a href="../cadastro_produtos/cad_list_prods.php">➡️ Entrada</a>



                <a href="../saida/saida.php"> ⬅️ Saida</a>



                <a href="../consulta/consulta.php"> 📦 Consulta</a>



                <a href="../compras/compras.php">🛒 Compras</a>



                <a href="buscar_relatorio.php?tipo=dashboard">📊 Relatórios</a>



                <a href="../configuracoes/painel_principal_config.php">⚙️ Configurações</a>



            </nav>



            <a href="../../index.html" class="logout">🚪 Sair</a>



        </aside>







        <main class="main">



            <div class="top">



                <h2>Módulo de Relatórios Gerenciais</h2>



                <p class="subtitulo">Selecione uma categoria e o período desejado para analisar as movimentações.</p>



            </div>







            <div class="menu-relatorios" style="margin: 20px 0; display: flex; gap: 8px; flex-wrap: wrap;">



                <a href="?tipo=dashboard&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='dashboard'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='dashboard'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">Movimentações</a>



                <a href="?tipo=vendas&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='vendas'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='vendas'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;"> Vendas</a>



                <a href="?tipo=baixas&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='baixas'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='baixas'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;"> Perdas / Baixas</a>



                <a href="?tipo=produtos&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='produtos'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='produtos'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">Produtos</a>



                <a href="?tipo=lotes&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='lotes'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='lotes'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">Lotes</a>



                <a href="?tipo=vencimento&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='vencimento'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='vencimento'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">Vencimento</a>



                <a href="?tipo=descontos&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='descontos'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='descontos'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">Descontos</a>



                <a href="?tipo=lucro&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: <?= $tipo=='lucro'?'#00F5D4':'#1a233a' ?>; color: <?= $tipo=='lucro'?'#02152E':'#fff' ?>; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">Lucro</a>



            </div>







            <div class="sub-menu-periodos" style="margin-bottom: 25px; background: #001A36; padding: 12px; border-radius: 8px; border: 1px solid rgba(0, 183, 195, 0.2); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">



                <div style="display: flex; gap: 10px; align-items: center;">



                    <span style="font-size: 14px; color: #94a3b8; font-weight: 600;">Filtrar Tempo:</span>



                    <a href="?tipo=<?= $tipo ?>&periodo=todos" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; background: <?= $periodo=='todos'?'#00B7C3':'#1a233a' ?>; color: #fff;">Histórico Completo</a>



                    <a href="?tipo=<?= $tipo ?>&periodo=hoje" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; background: <?= $periodo=='hoje'?'#00B7C3':'#1a233a' ?>; color: #fff;">Hoje</a>



                    <a href="?tipo=<?= $tipo ?>&periodo=semana" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; background: <?= $periodo=='semana'?'#00B7C3':'#1a233a' ?>; color: #fff;">Últimos 7 dias</a>



                    <a href="?tipo=<?= $tipo ?>&periodo=mes" style="padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; background: <?= $periodo=='mes'?'#00B7C3':'#1a233a' ?>; color: #fff;">Últimos 30 dias</a>



                </div>







                <div style="display: flex; gap: 10px;">



                    <a href="gerar_excel.php?tipo=<?= $tipo ?>&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: #22c55e; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">📊 Baixar Excel</a>



                    <a href="gerar_pdf.php?tipo=<?= $tipo ?>&periodo=<?= $periodo ?>" style="padding: 8px 14px; background: #ef4444; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px;">📄 Baixar PDF</a>



                </div>



            </div>







            <div class="conteudo-dinamico-relatorio">



                <?php



                switch($tipo){



                    case "vendas":



                        include "includes/relatorio_vendas.php";



                        break;



                    case "baixas":



                        include "includes/relatorio_baixas.php";



                        break;



                    case "dashboard":



                    default:



                        include "relatorios.php";



                        break;



                }



                ?>



            </div>



        </main>



    </div>



</body>



</html>



relatorios.js

function animar(id, valor) {

    let el = document.getElementById(id);

    if (!el) return;

   

    let i = 0;

    if (valor == 0) {

        el.innerText = 0;

        return;

    }



    let intervalo = setInterval(() => {

        i += Math.ceil(valor / 15);

        if (i >= valor) {

            i = valor;

            clearInterval(intervalo);

        }

        el.innerText = i;

    }, 40);

}



window.onload = () => {

    let vProd = parseInt(document.getElementById("prod")?.innerText) || 0;

    let vLotes = parseInt(document.getElementById("lotes_cantina")?.innerText) || 0;

    let vVenc = parseInt(document.getElementById("vencidos")?.innerText) || 0;

    let vPromo = parseInt(document.getElementById("promo")?.innerText) || 0;

    let vEstoque = parseInt(document.getElementById("estoque")?.innerText) || 0;



    animar("prod", vProd);

    animar("lotes_cantina", vLotes);

    animar("vencidos", vVenc);

    animar("promo", vPromo);

    animar("estoque", vEstoque);

};



relatorio_vendas.php

<?php

date_default_timezone_set('America/Sao_Paulo');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);



// Conexão padrão com o banco

$conn = new mysqli("localhost", "root", "usbw", "databasetcc");

if ($conn->connect_error) { die("Erro de conexão: " . $conn->connect_error); }



// Captura o período selecionado vindo do buscar_relatorio.php

$periodo_atual = isset($GLOBALS['periodo_atual']) ? $GLOBALS['periodo_atual'] : "todos";



// Monta o filtro de data correto para as vendas

$filtro_venda = "";

if ($periodo_atual == "hoje") {

    $filtro_venda = " AND DATE(s.data_saida) = CURDATE()";

} elseif ($periodo_atual == "semana") {

    $filtro_venda = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";

} elseif ($periodo_atual == "mes") {

    $filtro_venda = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

}



// Busca as saídas de venda trazendo também os preços e descontos do lote envolvido

$sql_vendas = "SELECT p.NomeProduto, l.numero_lote, l.preco_venda, l.desconto, s.quantidade_saida, s.data_saida, s.motivo_saida

               FROM saida s

               INNER JOIN produtoslotes l ON s.idlote = l.idlote

               INNER JOIN produtos p ON l.idproduto = p.IdProduto

               WHERE LOWER(s.motivo_saida) = 'venda' " . $filtro_venda . "

               ORDER BY s.id_saida DESC";



$resultado = $conn->query($sql_vendas);



if (!$resultado) {

    die("<div style='color:red; padding:20px; background:#fff;'><strong>Erro na Consulta de Vendas:</strong> " . $conn->error . "</div>");

}

?>



<style>

    .container-tabela {

        width: 100%;

        margin-top: 20px;

        background: #001a36;

        border: 1px solid rgba(0, 245, 212, 0.2);

        border-radius: 8px;

        overflow: hidden;

        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);

    }

    .tabela-dados {

        width: 100%;

        border-collapse: collapse;

        text-align: left;

        font-family: sans-serif;

    }

    .tabela-dados th {

        background-color: rgba(0, 245, 212, 0.08);

        color: #00F5D4;

        padding: 14px 18px;

        font-size: 14px;

        text-transform: uppercase;

        border-bottom: 2px solid rgba(0, 245, 212, 0.3);

        letter-spacing: 0.5px;

    }

    .tabela-dados td {

        padding: 14px 18px;

        color: #e2e8f0;

        font-size: 14px;

        border-bottom: 1px solid rgba(255, 255, 255, 0.05);

    }

    .tabela-dados tr:hover {

        background-color: rgba(255, 255, 255, 0.02);

    }

    .badge-venda {

        display: inline-block;

        padding: 4px 10px;

        border-radius: 4px;

        font-size: 12px;

        font-weight: bold;

        text-transform: uppercase;

        background: rgba(34, 197, 94, 0.1);

        color: #22c55e;

        border: 1px solid rgba(34, 197, 94, 0.3);

    }

    .preco-original {

        text-decoration: line-through;

        color: #94a3b8;

        font-size: 12px;

        display: block;

    }

    .preco-final {

        color: #00F5D4;

        font-weight: bold;

    }

    .tag-desconto {

        background: rgba(234, 179, 8, 0.1);

        color: #eab308;

        border: 1px solid rgba(234, 179, 8, 0.3);

        padding: 2px 6px;

        border-radius: 3px;

        font-size: 10px;

        margin-left: 5px;

        font-weight: bold;

    }

</style>



<div class="container-tabela">

    <table class="tabela-dados">

        <thead>

            <tr>

                <th>Produto</th>

                <th>Nº Lote</th>

                <th>Qtd Vendida</th>

                <th>Valor Total</th>

                <th>Data da Venda</th>

                <th>Motivo</th>

            </tr>

        </thead>

        <tbody>

            <?php

            if ($resultado->num_rows > 0) {

                while ($row = $resultado->fetch_assoc()) {

                    $data_venda = ($row['data_saida']) ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';

                   

                    $qtd = intval($row['quantidade_saida']);

                    $preco_unitario = floatval($row['preco_venda']);

                    $desconto_porcentagem = floatval($row['desconto']);

                   

                    // Cálculo do valor original total

                    $valor_total_original = $preco_unitario * $qtd;

                   

                    if ($desconto_porcentagem > 0) {

                        // Aplica o desconto unitário e multiplica pela quantidade

                        $preco_com_desconto = $preco_unitario * (1 - ($desconto_porcentagem / 100));

                        $valor_total_final = $preco_com_desconto * $qtd;

                    } else {

                        $valor_total_final = $valor_total_original;

                    }

                   

                    echo "<tr>";

                    echo "<td style='font-weight: 600; color: #fff;'>" . htmlspecialchars($row['NomeProduto']) . "</td>";

                    echo "<td style='color: #94a3b8;'>#" . htmlspecialchars($row['numero_lote']) . "</td>";

                    echo "<td style='color: #e2e8f0;'>" . $qtd . " un</td>";

                   

                    // Coluna de Valor Total com a lógica de desconto visual

                    echo "<td>";

                    if ($desconto_porcentagem > 0) {

                        echo "<span class='preco-original'>R$ " . number_format($valor_total_original, 2, ',', '.') . "</span>";

                        echo "<span class='preco-final'>R$ " . number_format($valor_total_final, 2, ',', '.') . "</span>";

                        echo "<span class='tag-desconto'>-" . $desconto_porcentagem . "%</span>";

                    } else {

                        echo "<span class='preco-final' style='color: #fff;'>R$ " . number_format($valor_total_final, 2, ',', '.') . "</span>";

                    }

                    echo "</td>";

                   

                    echo "<td>" . $data_venda . "</td>";

                    echo "<td><span class='badge-venda'>" . htmlspecialchars($row['motivo_saida']) . "</span></td>";

                    echo "</tr>";

                }

            } else {

                echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8; padding:25px;'>Nenhuma venda registrada neste período.</td></tr>";

            }

            ?>

        </tbody>

    </table>

</div> 

