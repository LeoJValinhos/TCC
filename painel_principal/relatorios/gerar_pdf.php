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
    RELATÓRIO: PRODUTOS (ALINHADO COM OS LOTES E DESCONTOS)
   ===================================================== */
if ($tipo == "produtos") {
    echo "<thead>
            <tr>
                <th>Produto</th>
                <th class='text-center'>Nº Lote</th>
                <th>Marca</th>
                <th class='text-right'>Preço Compra</th>
                <th class='text-right'>Preço Venda</th>
                <th class='text-center'>Desconto</th>
                <th class='text-right'>Venda Final</th>
            </tr>
          </thead>
          <tbody>";

    $sql = "SELECT p.NomeProduto, p.MarcaProduto, l.numero_lote, l.preco_compra, l.preco_venda, l.desconto
            FROM produtoslotes l
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = $idEmpresa 
            ORDER BY p.NomeProduto ASC, l.numero_lote ASC";
            
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $p_compra = floatval($row['preco_compra']);
        $p_venda_base = floatval($row['preco_venda']);
        $desc = floatval($row['desconto']);
        
        $p_venda_final = $p_venda_base * (1 - ($desc / 100));
        $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';
        $marca = !empty($row['MarcaProduto']) ? htmlspecialchars($row['MarcaProduto']) : '-';
        
        $txt_desconto = ($desc > 0) ? $desc . '%' : '-';

        echo "<tr>
                <td>".htmlspecialchars($row['NomeProduto'])."</td>
                <td class='text-center'>".$lote."</td>
                <td>".$marca."</td>
                <td class='text-right'>R$ ".number_format($p_compra, 2, ',', '.')."</td>
                <td class='text-right'>R$ ".number_format($p_venda_base, 2, ',', '.')."</td>
                <td class='text-center'>".$txt_desconto."</td>
                <td class='text-right' style='font-weight:bold;'>R$ ".number_format($p_venda_final, 2, ',', '.')."</td>
              </tr>";
    }
}



            /* =====================================================
    RELATÓRIO: LOTES (COM CAMPO MARCA)
   ===================================================== */
elseif ($tipo == "lotes") {
    echo "<thead>
            <tr>
                <th>Produto</th>
                <th>Nº Lote</th>
                <th>Marca</th>
                <th class='text-center'>Quantidade</th>
                <th class='text-center'>Validade</th>
                <th class='text-center'>Status</th>
            </tr>
          </thead>
          <tbody>";

    $sql = "SELECT p.NomeProduto, p.MarcaProduto, l.numero_lote, l.quantidade, l.validade, l.status_lote 
            FROM produtoslotes l 
            INNER JOIN produtos p ON p.IdProduto = l.idproduto 
            WHERE l.idEmpresa = $idEmpresa 
            ORDER BY l.idlote DESC";
            
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $val = $row['validade'] ? date('d/m/Y', strtotime($row['validade'])) : '-';
        $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';
        $marca = !empty($row['MarcaProduto']) ? htmlspecialchars($row['MarcaProduto']) : '-';

        echo "<tr>
                <td style='font-weight: 600;'>".htmlspecialchars($row['NomeProduto'])."</td>
                <td>".$lote."</td>
                <td>".$marca."</td>
                <td class='text-center'>".intval($row['quantidade'])." un</td>
                <td class='text-center'>".$val."</td>
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
    RELATÓRIO: DESCONTOS (ALINHADO COM A INTERFACE E ESTOQUE)
   ===================================================== */
elseif ($tipo == "descontos") {
    echo "<thead>
            <tr>
                <th>Produto</th>
                <th>Nº Lote</th>
                <th>Qtd em Estoque</th>
                <th class='text-right'>Valor Original</th>
                <th class='text-right'>Val. Unit. c/ Desconto</th>
                <th class='text-center'>Desconto Aplicado</th>
                <th class='text-right'>Valor Total Estoque</th>
            </tr>
          </thead>
          <tbody>";

    $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.preco_venda, l.desconto, l.status_lote
            FROM produtoslotes l
            INNER JOIN produtos p ON p.idProduto = l.idproduto
            WHERE l.idEmpresa = '" . $idEmpresa . "' AND l.desconto > 0
            ORDER BY l.desconto DESC, p.NomeProduto ASC";
            
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $qtd = intval($row['quantidade']);
            $preco_original = floatval($row['preco_venda']);
            $porcentagem_desc = floatval($row['desconto']);
            
            $preco_com_desconto = $preco_original * (1 - ($porcentagem_desc / 100));
            $valor_total_estoque = $preco_com_desconto * $qtd;
            
            $status_limpo = strtolower(trim($row['status_lote']));
            $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';

            echo "<tr>
                    <td style='font-weight: 600;'>";
                        echo htmlspecialchars($row['NomeProduto']);
                        if ($status_limpo == 'vencido') {
                            echo " <span style='background: #ef4444; color: #ffffff; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: bold;'>VENCIDO</span>";
                        }
            echo "  </td>
                    <td>" . $lote . "</td>
                    <td>" . $qtd . " un</td>
                    <td class='text-right'>R$ " . number_format($preco_original, 2, ',', '.') . "</td>
                    <td class='text-right'>R$ " . number_format($preco_com_desconto, 2, ',', '.') . "</td>
                    <td class='text-center' style='color: #eab308; font-weight: bold;'>" . number_format($porcentagem_desc, 0) . "% OFF</td>
                    <td class='text-right' style='color: #22c55e; font-weight: bold;'>R$ " . number_format($valor_total_estoque, 2, ',', '.') . "</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='7' style='text-align:center; padding:20px;'>Nenhum lote com promoção ativa encontrado para o período selecionado.</td></tr>";
    }
    echo "</tbody>";
}



            /* =====================================================
    RELATÓRIO: LUCRO (ALINHADO COM A INTERFACE)
   ===================================================== */
elseif ($tipo == "lucro") {
    echo "<thead>
            <tr>
                <th>Produto</th>
                <th>Qtd Vendida</th>
                <th>Data Venda</th>
                <th class='text-right'>Preço Custo (Un)</th>
                <th class='text-center'>Desconto</th>
                <th class='text-right'>Valor Faturado</th>
                <th class='text-right'>Lucro Estimado</th>
            </tr>
          </thead>
          <tbody>";

    $sql = "SELECT p.NomeProduto, s.quantidade_saida, s.data_saida, l.preco_compra, l.preco_venda, l.desconto
            FROM saida s
            INNER JOIN produtoslotes l ON s.idlote = l.idlote
            INNER JOIN produtos p ON l.idproduto = p.IdProduto
            WHERE l.idEmpresa = '$idEmpresa' AND LOWER(s.motivo_saida) = 'venda' " . (isset($filtro_saida) ? $filtro_saida : '') . "
            ORDER BY s.id_saida DESC";
            
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data_venda = ($row['data_saida']) ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';
            $qtd = intval($row['quantidade_saida']);
            $custo_unitario = floatval($row['preco_compra']);
            $preco_venda = floatval($row['preco_venda']);
            $porcentagem_desc = floatval($row['desconto']);

            $custo_total = $qtd * $custo_unitario;
            $venda_bruta = $qtd * $preco_venda;
            $total_desconto = $venda_bruta * ($porcentagem_desc / 100);
            
            $faturamento_real = $venda_bruta - $total_desconto;
            $lucro = $faturamento_real - $custo_total;

            // Define dinamicamente se o lucro vai impresso em vermelho (prejuízo) ou verde no PDF
            $cor_lucro = ($lucro >= 0) ? '#22c55e' : '#ef4444';

            echo "<tr>
                    <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>
                    <td>" . $qtd . " un</td>
                    <td>" . $data_venda . "</td>
                    <td class='text-right'>R$ " . number_format($custo_unitario, 2, ',', '.') . "</td>
                    <td class='text-center'>";
                        if ($porcentagem_desc > 0) {
                            echo "<span style='color: #eab308; font-weight: bold;'>" . number_format($porcentagem_desc, 0) . "% OFF</span>";
                        } else {
                            echo "<span style='color: #64748b;'>-</span>";
                        }
            echo "  </td>
                    <td class='text-right' style='color: #38bdf8;'>R$ " . number_format($faturamento_real, 2, ',', '.') . "</td>
                    <td class='text-right' style='color: " . $cor_lucro . "; font-weight: bold;'>R$ " . number_format($lucro, 2, ',', '.') . "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center' style='padding:20px;'>Nenhuma venda encontrada para calcular lucros neste período.</td></tr>";
    }
    echo "</tbody>";
}


       /* =====================================================
   RELATÓRIO: VENDAS (AJUSTADO COM PREÇO UNITÁRIO ORIGINAL)
   ===================================================== */
elseif ($tipo == "vendas") {
    echo "<thead>
            <tr>
                <th>Produto</th>
                <th>Lote</th>
                <th class='text-center'>Qtd</th>
                <th class='text-center'>P. Unitário</th>
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
            $p_unitario_original = floatval($row['preco_venda']);
            $desc = floatval($row['desconto']);
            
            // O Total Faturado continua calculando o desconto normalmente
            $valor_final = ($p_unitario_original * (1 - ($desc / 100))) * $qtd;
            
            $data_v = $row['data_saida'] ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';

            echo "<tr>
                    <td>".htmlspecialchars($row['NomeProduto'])."</td>
                    <td>#".htmlspecialchars($row['numero_lote'])."</td>
                    <td class='text-center'>{$qtd} un</td>";
            
            // Agora exibe o Preço Unitário NORMAL (sem desconto)
            echo "<td class='text-center' style='color: #94a3b8;'>R$ ".number_format($p_unitario_original, 2, ',', '.')."</td>";
            
            echo "<td class='text-center'>{$desc}%</td>
                    <td class='text-right' style='color:#22c55e; font-weight:bold;'>R$ ".number_format($valor_final, 2, ',', '.')."</td>
                    <td class='text-center'>{$data_v}</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center'>Nenhuma venda registrada.</td></tr>";
    }
    echo "</tbody>";
}


            /* =====================================================
            NOVO RELATÓRIO: PERDAS / BAIXAS (Com Custo Unitário)
            ===================================================== */
            elseif ($tipo == "baixas" || $tipo == "perdas") {
                echo "<thead>
                        <tr>
                            <th>Produto</th>
                            <th>Lote</th>
                            <th class='text-center'>Qtd Perdida</th>
                            <th class='text-right'>Custo Unitário</th>
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

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $qtd = intval($row['quantidade_saida']);
                        $custo = floatval($row['preco_compra']);
                        $prejuizo = $custo * $qtd;
                        $data_b = $row['data_saida'] ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';

                        echo "<tr>
                                <td>".htmlspecialchars($row['NomeProduto'])."</td>
                                <td>#".htmlspecialchars($row['numero_lote'])."</td>
                                <td class='text-center'>{$qtd} un</td>
                                <td class='text-right'>R$ ".number_format($custo, 2, ',', '.')."</td>
                                <td class='text-right' style='color:#ef4444; font-weight:bold;'>R$ ".number_format($prejuizo, 2, ',', '.')."</td>
                                <td class='text-center'>{$data_b}</td>
                                <td><span style='color:#b91c1c;'>".htmlspecialchars($row['motivo_saida'])."</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Nenhuma perda ou baixa registrada.</td></tr>";
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