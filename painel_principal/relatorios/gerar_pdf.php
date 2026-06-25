<?php

date_default_timezone_set('America/Sao_Paulo');



if (!isset($_SESSION)) {

    session_start();

}



include '../../funcoes/verifica_login.php';

include '../../funcoes/conexao.php';



$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;



if (!$idEmpresa) {

    die("Erro: Sessão expirada. Faça login novamente.");

}



$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'produtos';



// Ajuste amigável de nomenclatura para o topo do documento PDF

$nome_relatorio = strtoupper($tipo);

if ($tipo == 'baixas') $nome_relatorio = "PERDAS / BAIXAS";

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Relatório Gerencial - <?php echo $nome_relatorio; ?></title>

   

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>



    <style>

        body {

            font-family: 'Helvetica', 'Arial', sans-serif;

            color: #333;

            margin: 20px;

            font-size: 13px;

        }

        .header {

            text-align: center;

            border-bottom: 2px solid #001A36;

            padding-bottom: 10px;

            margin-bottom: 20px;

        }

        .header h1 {

            margin: 0;

            font-size: 20px;

            color: #001A36;

        }

        .header p {

            margin: 5px 0 0 0;

            font-size: 12px;

            color: #666;

        }

        .meta-info {

            margin-bottom: 15px;

            font-weight: bold;

        }

        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 10px;

        }

        th, td {

            border: 1px solid #ccc;

            padding: 8px;

            text-align: left;

        }

        th {

            background-color: #001A36;

            color: white;

            font-weight: bold;

        }

        .text-right { text-align: right; }

        .text-center { text-align: center; }

    </style>

</head>

<body>



    <div id="conteudo-relatorio">

        <div class="header">

            <h1>INVEX - SISTEMA DE GESTÃO DE ESTOQUE</h1>

            <p>Relatório Gerencial Automatizado</p>

        </div>



        <div class="meta-info">

            CATEGORIA: <?php echo $nome_relatorio; ?><br>

            <span style="font-weight: normal; font-size: 11px; color: #555;">Gerado em: <?php echo date('d/m/Y H:i'); ?></span>

        </div>



        <table>

            <?php

            /* =====================================================

            RELATÓRIO: PRODUTOS

            ===================================================== */

            if ($tipo == "produtos") {

                echo "<thead>

                        <tr>

                            <th>Produto</th>

                            <th>Marca</th>

                            <th class='text-right'>Preço Compra</th>

                            <th class='text-right'>Preço Venda</th>

                        </tr>

                      </thead>

                      <tbody>";



                $sql = "SELECT NomeProduto, MarcaProduto, preco_padrao_compra, preco_padrao_venda FROM produtos WHERE idEmpresa = $idEmpresa ORDER BY NomeProduto ASC";

                $result = $conn->query($sql);



                while ($row = $result->fetch_assoc()) {

                    echo "<tr>

                            <td>".htmlspecialchars($row['NomeProduto'])."</td>

                            <td>".htmlspecialchars($row['MarcaProduto'])."</td>

                            <td class='text-right'>R$ ".number_format($row['preco_padrao_compra'], 2, ',', '.')."</td>

                            <td class='text-right'>R$ ".number_format($row['preco_padrao_venda'], 2, ',', '.')."</td>

                          </tr>";

                }

            }



            /* =====================================================

            RELATÓRIO: LOTES

            ===================================================== */

            elseif ($tipo == "lotes") {

                echo "<thead>

                        <tr>

                            <th>Produto</th>

                            <th>Número Lote</th>

                            <th class='text-center'>Quantidade</th>

                            <th class='text-center'>Validade</th>

                            <th class='text-center'>Status</th>

                        </tr>

                      </thead>

                      <tbody>";



                $sql = "SELECT l.numero_lote, l.quantidade, l.validade, l.status_lote, p.NomeProduto FROM produtoslotes l INNER JOIN p ON p.idProduto = l.idproduto WHERE l.idEmpresa = $idEmpresa ORDER BY p.NomeProduto ASC";

                $result = $conn->query($sql);



                while ($row = $result->fetch_assoc()) {

                    $val = $row['validade'] ? date('d/m/Y', strtotime($row['validade'])) : 'N/A';

                    echo "<tr>

                            <td>".htmlspecialchars($row['NomeProduto'])."</td>

                            <td>".htmlspecialchars($row['numero_lote'])."</td>

                            <td class='text-center'>{$row['quantidade']} un</td>

                            <td class='text-center'>{$val}</td>

                            <td class='text-center'>".strtoupper($row['status_lote'])."</td>

                          </tr>";

                }

            }



            /* =====================================================

            RELATÓRIO: VENCIMENTO

            ===================================================== */

            elseif ($tipo == "vencimento") {

                echo "<thead>

                        <tr>

                            <th>Produto</th>

                            <th>Lote</th>

                            <th class='text-center'>Quantidade</th>

                            <th class='text-center'>Data Vencimento</th>

                        </tr>

                      </thead>

                      <tbody>";



                $sql = "SELECT l.numero_lote, l.quantidade, l.validade, p.NomeProduto FROM produtoslotes l INNER JOIN produtos p ON p.idProduto = l.idproduto WHERE l.idEmpresa = $idEmpresa ORDER BY l.validade ASC";

                $result = $conn->query($sql);



                while ($row = $result->fetch_assoc()) {

                    $val = $row['validade'] ? date('d/m/Y', strtotime($row['validade'])) : 'N/A';

                    echo "<tr>

                            <td>".htmlspecialchars($row['NomeProduto'])."</td>

                            <td>".htmlspecialchars($row['numero_lote'])."</td>

                            <td class='text-center'>{$row['quantidade']} un</td>

                            <td class='text-center' style='font-weight: bold;'>{$val}</td>

                          </tr>";

                }

            }



            /* =====================================================

            RELATÓRIO: DESCONTOS

            ===================================================== */

            elseif ($tipo == "descontos") {

    echo "<thead><tr><th>Produto</th><th>Lote</th><th class='text-center'>Qtd</th><th class='text-center'>Desconto</th><th class='text-right'>Valor Desc. (R$)</th></tr></thead><tbody>";

    $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.desconto, l.preco_venda

            FROM produtoslotes l

            INNER JOIN produtos p ON p.idProduto = l.idproduto

            WHERE l.idEmpresa = '" . $idEmpresa . "' AND l.desconto > 0";

    $result = $conn->query($sql);

   

    if ($result && $result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            $valor_desc = ($row['quantidade'] * $row['preco_venda']) * ($row['desconto'] / 100);

            echo "<tr>

                <td>" . htmlspecialchars($row['NomeProduto']) . "</td>

                <td>" . htmlspecialchars($row['numero_lote']) . "</td>

                <td class='text-center'>{$row['quantidade']} un</td>

                <td class='text-center'>{$row['desconto']}%</td>

                <td class='text-right'>R$ " . number_format($valor_desc, 2, ',', '.') . "</td>

            </tr>";

        }

    } else {

        echo "<tr><td colspan='5' class='text-center'>Nenhum desconto registrado neste período.</td></tr>";

    }

    echo "</tbody>";

}



            /* =====================================================

            RELATÓRIO: LUCRO

            ===================================================== */

            elseif ($tipo == "lucro") {

                echo "<thead>

                        <tr>

                            <th>Produto</th>

                            <th class='text-right'>Custo Compra</th>

                            <th class='text-right'>Preço Venda</th>

                            <th class='text-right'>Margem Estimada</th>

                        </tr>

                      </thead>

                      <tbody>";



                $sql = "SELECT NomeProduto, preco_padrao_compra, preco_padrao_venda, (preco_padrao_venda - preco_padrao_compra) AS lucro_unitario FROM produtos WHERE idEmpresa = $idEmpresa ORDER BY lucro_unitario DESC";

                $result = $conn->query($sql);



                while ($row = $result->fetch_assoc()) {

                    echo "<tr>

                            <td>".htmlspecialchars($row['NomeProduto'])."</td>

                            <td class='text-right'>R$ ".number_format($row['preco_padrao_compra'], 2, ',', '.')."</td>

                            <td class='text-right'>R$ ".number_format($row['preco_padrao_venda'], 2, ',', '.')."</td>

                            <td class='text-right' style='font-weight: bold;'>R$ ".number_format($row['lucro_unitario'], 2, ',', '.')."</td>

                          </tr>";

                }

            }



            /* =====================================================

   RELATÓRIO: VENDAS (AJUSTADO COM DESCONTO)

   ===================================================== */

elseif ($tipo == "vendas") {

    echo "<thead>

            <tr>

                <th>Produto</th>

                <th>Lote</th>

                <th class='text-center'>Qtd</th>

                <th class='text-center'>Desconto</th>

                <th class='text-right'>Total Faturado</th>

                <th class='text-center'>Data</th>

            </tr>

          </thead>

          <tbody>";



    $sql = "SELECT p.NomeProduto, l.numero_lote, l.preco_venda, l.desconto, s.quantidade_saida, s.data_saida

            FROM saida s

            INNER JOIN produtoslotes l ON s.idlote = l.idlote

            INNER JOIN produtos p ON l.idproduto = p.IdProduto

            WHERE l.idEmpresa = $idEmpresa AND LOWER(s.motivo_saida) = 'venda'

            ORDER BY s.id_saida DESC";

   

    $result = $conn->query($sql);



    if ($result && $result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {

            $qtd = intval($row['quantidade_saida']);

            $p_unitario = floatval($row['preco_venda']);

            $desc = floatval($row['desconto']);

           

            // Cálculo do valor final com desconto aplicado

            $valor_final = ($p_unitario * (1 - ($desc / 100))) * $qtd;

            $data_v = $row['data_saida'] ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';



            echo "<tr>

                    <td>".htmlspecialchars($row['NomeProduto'])."</td>

                    <td>#".htmlspecialchars($row['numero_lote'])."</td>

                    <td class='text-center'>{$qtd} un</td>

                    <td class='text-center'>{$desc}%</td>

                    <td class='text-right' style='color:#22c55e; font-weight:bold;'>R$ ".number_format($valor_final, 2, ',', '.')."</td>

                    <td class='text-center'>{$data_v}</td>

                  </tr>";

        }

    } else {

        echo "<tr><td colspan='6' class='text-center'>Nenhuma venda registrada.</td></tr>";

    }

    echo "</tbody>";

}



            /* =====================================================

            NOVO RELATÓRIO: PERDAS / BAIXAS

            ===================================================== */

            elseif ($tipo == "baixas" || $tipo == "perdas") {

                echo "<thead>

                        <tr>

                            <th>Produto</th>

                            <th>Lote</th>

                            <th class='text-center'>Qtd Perdida</th>

                            <th class='text-right'>Prejuízo Total</th>

                            <th class='text-center'>Data da Baixa</th>

                            <th>Motivo</th>

                        </tr>

                      </thead>

                      <tbody>";



                $sql = "SELECT p.NomeProduto, l.numero_lote, l.preco_compra, s.quantidade_saida, s.data_saida, s.motivo_saida

                        FROM saida s

                        INNER JOIN produtoslotes l ON s.idlote = l.idlote

                        INNER JOIN produtos p ON l.idproduto = p.IdProduto

                        WHERE l.idEmpresa = $idEmpresa AND LOWER(s.motivo_saida) <> 'venda'

                        ORDER BY s.id_saida DESC";

                $result = $conn->query($sql);



                while ($row = $result->fetch_assoc()) {

                    $qtd = intval($row['quantidade_saida']);

                    $custo = floatval($row['preco_compra']);

                    $prejuizo = $custo * $qtd;

                    $data_b = $row['data_saida'] ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';



                    echo "<tr>

                            <td>".htmlspecialchars($row['NomeProduto'])."</td>

                            <td>#".htmlspecialchars($row['numero_lote'])."</td>

                            <td class='text-center'>{$qtd} un</td>

                            <td class='text-right' style='color:#ef4444; font-weight:bold;'>R$ ".number_format($prejuizo, 2, ',', '.')."</td>

                            <td class='text-center'>{$data_b}</td>

                            <td><span style='color:#b91c1c;'>".htmlspecialchars($row['motivo_saida'])."</span></td>

                          </tr>";

                }

            }



            /* =====================================================

            NOVO RELATÓRIO: DASHBOARD

            ===================================================== */

            elseif ($tipo == "dashboard") {

                echo "<thead>

                        <tr>

                            <th>Tipo</th>

                            <th>Produto</th>

                            <th>Lote</th>

                            <th class='text-center'>Quantidade</th>

                            <th class='text-center'>Data Registro</th>

                            <th>Motivo</th>

                        </tr>

                      </thead>

                      <tbody>";



                $sql = "SELECT p.NomeProduto, l.numero_lote, s.quantidade_saida, s.data_saida, s.motivo_saida

                        FROM saida s

                        INNER JOIN produtoslotes l ON s.idlote = l.idlote

                        INNER JOIN produtos p ON l.idproduto = p.IdProduto

                        WHERE l.idEmpresa = $idEmpresa

                        ORDER BY s.id_saida DESC";

                $result = $conn->query($sql);



                while ($row = $result->fetch_assoc()) {

                    $is_venda = (strtolower($row['motivo_saida']) == 'venda');

                    $tipo_mov = $is_venda ? 'VENDA' : 'BAIXA';

                    $cor_tipo = $is_venda ? '#22c55e' : '#ef4444';

                    $data_m = $row['data_saida'] ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';



                    echo "<tr>

                            <td style='font-weight:bold; color:{$cor_tipo};'>{$tipo_mov}</td>

                            <td>".htmlspecialchars($row['NomeProduto'])."</td>

                            <td>#".htmlspecialchars($row['numero_lote'])."</td>

                            <td class='text-center'>{$row['quantidade_saida']} un</td>

                            <td class='text-center'>{$data_m}</td>

                            <td>".htmlspecialchars($row['motivo_saida'])."</td>

                          </tr>";

                }

            }

            ?>

            </tbody>

        </table>

    </div>



    <script>

        window.onload = function() {

            const elemento = document.getElementById('conteudo-relatorio');

            const opcoes = {

                margin:       10,

                filename:     'relatorio_<?php echo $tipo; ?>.pdf',

                image:        { type: 'jpeg', quality: 0.98 },

                html2canvas:  { scale: 2, useCORS: true },

                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }

            };



            html2pdf().set(opcoes).from(elemento).save().then(() => {

                setTimeout(() => { window.close(); }, 1500);

            });

        };

    </script>

</body>

</html>