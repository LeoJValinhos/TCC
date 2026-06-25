<?php

// Configurações de fuso horário e ocultação de avisos

date_default_timezone_set('America/Sao_Paulo');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);



// Conexão padrão do INVEX

$conn = new mysqli("localhost", "root", "usbw", "databasetcc");

if ($conn->connect_error) { die("Erro de conexão: " . $conn->connect_error); }



// Captura as variáveis globais vindas do buscar_relatorio.php

$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;

$periodo_atual = isset($GLOBALS['periodo_atual']) ? $GLOBALS['periodo_atual'] : "todos";

if (isset($_GET['periodo'])) { $periodo_atual = $_GET['periodo']; }

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : "dashboard";



// MONTAGEM DOS FILTROS DE DATA DINÂMICOS (CORRIGIDO USANDO DATA_SAIDA REAL)

$filtro_saida = "";

$filtro_lote = "";  



if ($periodo_atual == "hoje") {

    $filtro_saida = " AND DATE(s.data_saida) = CURDATE()";

    $filtro_lote  = " AND DATE(l.criado_em) = CURDATE()";

} elseif ($periodo_atual == "semana") {

    $filtro_saida = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";

    $filtro_lote  = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";

} elseif ($periodo_atual == "mes") {

    $filtro_saida = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

    $filtro_lote  = " AND DATE(l.criado_em) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";

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

    }

    .tabela-dados td {

        padding: 12px 18px;

        color: #e2e8f0;

        font-size: 14px;

        border-bottom: 1px solid rgba(255, 255, 255, 0.05);

    }

    .badge {

        display: inline-block;

        padding: 4px 10px;

        border-radius: 4px;

        font-size: 12px;

        font-weight: bold;

        text-transform: uppercase;

    }

    .badge-venda { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }

    .badge-perda { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

    .badge-normal { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }

    .badge-vencido { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

    .badge-promocao { background: rgba(234, 179, 8, 0.1); color: #eab308; border: 1px solid rgba(234, 179, 8, 0.3); }

</style>



<div class="container-tabela">

    <table class="tabela-dados">

        <?php

        /* =================================================================

           1. CENÁRIO: DASHBOARD / MOVIMENTAÇÕES

           ================================================================= */

        if ($tipo == "dashboard"):

            $sql = "SELECT p.NomeProduto, l.numero_lote, s.quantidade_saida, s.data_saida, s.motivo_saida

                    FROM saida s

                    INNER JOIN produtoslotes l ON s.idlote = l.idlote

                    INNER JOIN produtos p ON l.idproduto = p.IdProduto

                    WHERE l.idEmpresa = '$idEmpresa' " . $filtro_saida . "

                    ORDER BY s.id_saida DESC";

            $resultado = $conn->query($sql);

        ?>

            <thead>

                <tr>

                    <th>Tipo Movimentação</th>

                    <th>Produto</th>

                    <th>Lote</th>

                    <th>Quantidade</th>

                    <th>Data Registro</th>

                    <th>Ocorrência / Motivo</th>

                </tr>

            </thead>

            <tbody>

                <?php

                if ($resultado && $resultado->num_rows > 0) {

                    while ($row = $resultado->fetch_assoc()) {

                        $data_exibir = ($row['data_saida']) ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';

                        $motivo_limpo = strtolower(trim($row['motivo_saida']));

                       

                        if ($motivo_limpo == 'venda') {

                            $tipo_mov = "VENDA"; $classe_badge = "badge-venda";

                        } else {

                            $tipo_mov = "BAIXA/PERDA"; $classe_badge = "badge-perda";

                        }

                        echo "<tr>

                                <td><span class='badge " . $classe_badge . "'>" . $tipo_mov . "</span></td>

                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>

                                <td style='color: #94a3b8;'>#" . htmlspecialchars($row['numero_lote']) . "</td>

                                <td>" . intval($row['quantidade_saida']) . " un</td>

                                <td>" . $data_exibir . "</td>

                                <td style='color: #94a3b8; font-style: italic;'>" . htmlspecialchars($row['motivo_saida']) . "</td>

                              </tr>";

                    }

                } else {

                    echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhuma movimentação encontrada para o período selecionado.</td></tr>";

                }

                ?>

            </tbody>



        <?php

        /* =================================================================

           2. CENÁRIO: RELATÓRIO DE DESCONTOS APLICADOS

           ================================================================= */

        elseif ($tipo == "descontos"):

            $sql = "SELECT p.NomeProduto, l.numero_lote, s.quantidade_saida, s.data_saida, l.preco_venda, l.desconto

                    FROM saida s

                    INNER JOIN produtoslotes l ON s.idlote = l.idlote

                    INNER JOIN produtos p ON l.idproduto = p.IdProduto

                    WHERE l.idEmpresa = '$idEmpresa' AND LOWER(s.motivo_saida) = 'venda' " . $filtro_saida . "

                    ORDER BY s.id_saida DESC";

            $resultado = $conn->query($sql);

        ?>

            <thead>

                <tr>

                    <th>Produto</th>

                    <th>Nº Lote</th>

                    <th>Qtd Vendida</th>

                    <th>Data da Venda</th>

                    <th>Desconto Aplicado</th>

                    <th>Valor Total</th>

                </tr>

            </thead>

            <tbody>

                <?php

                if ($resultado && $resultado->num_rows > 0) {

                    while ($row = $resultado->fetch_assoc()) {

                        $data_venda = ($row['data_saida']) ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';

                       

                        $qtd = intval($row['quantidade_saida']);

                        $preco_venda = floatval($row['preco_venda']);

                        $porcentagem_desc = floatval($row['desconto']);

                       

                        $bruto = $qtd * $preco_venda;

                        $valor_desconto = $bruto * ($porcentagem_desc / 100);

                        $valor_total = $bruto - $valor_desconto;

                       

                        echo "<tr>

                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>

                                <td style='color: #94a3b8;'>#" . htmlspecialchars($row['numero_lote']) . "</td>

                                <td>" . $qtd . " un</td>

                                <td>" . $data_venda . "</td>

                                <td style='color: #eab308; font-weight: bold;'>" . number_format($porcentagem_desc, 0) . "% (R$ " . number_format($valor_desconto, 2, ',', '.') . ")</td>

                                <td style='color: #22c55e;'>R$ " . number_format($valor_total, 2, ',', '.') . "</td>

                              </tr>";

                    }

                } else {

                    echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhum desconto encontrado para o período selecionado.</td></tr>";

                }

                ?>

            </tbody>



        <?php

        /* =================================================================

           3. CENÁRIO: RELATÓRIO DE LUCRO BRUTO / VENDAS

           ================================================================= */

        elseif ($tipo == "lucro"):

            $sql = "SELECT p.NomeProduto, s.quantidade_saida, s.data_saida, l.preco_compra, l.preco_venda, l.desconto

                    FROM saida s

                    INNER JOIN produtoslotes l ON s.idlote = l.idlote

                    INNER JOIN produtos p ON l.idproduto = p.IdProduto

                    WHERE l.idEmpresa = '$idEmpresa' AND LOWER(s.motivo_saida) = 'venda' " . $filtro_saida . "

                    ORDER BY s.id_saida DESC";

            $resultado = $conn->query($sql);

        ?>

            <thead>

                <tr>

                    <th>Produto</th>

                    <th>Qtd Vendida</th>

                    <th>Data Venda</th>

                    <th>Preço Custo (Unidade)</th>

                    <th>Valor Faturado</th>

                    <th>Lucro Estimado</th>

                </tr>

            </thead>

            <tbody>

                <?php

                if ($resultado && $resultado->num_rows > 0) {

                    while ($row = $resultado->fetch_assoc()) {

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



                        echo "<tr>

                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>

                                <td>" . $qtd . " un</td>

                                <td>" . $data_venda . "</td>

                                <td style='color: #94a3b8;'>R$ " . number_format($custo_unitario, 2, ',', '.') . "</td>

                                <td style='color: #38bdf8;'>R$ " . number_format($faturamento_real, 2, ',', '.') . "</td>

                                <td style='color: #22c55e; font-weight: bold;'>R$ " . number_format($lucro, 2, ',', '.') . "</td>

                              </tr>";

                    }

                } else {

                    echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhuma venda encontrada para calcular lucros neste período.</td></tr>";

                }

                ?>

            </tbody>



        <?php

        /* =================================================================

           4. CENÁRIO: DEMAIS ABAS DE LOTES (Produtos, Lotes, Vencimento)

           ================================================================= */

        else:

            $complemento_query = "";

            if ($tipo == "vencimento") {

                $complemento_query = " AND LOWER(l.status_lote) = 'vencido'";

            }



            $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.validade, l.status_lote

                    FROM produtoslotes l

                    INNER JOIN produtos p ON l.idproduto = p.IdProduto

                    WHERE l.idEmpresa = '$idEmpresa' " . $filtro_lote . $complemento_query . "

                    ORDER BY l.idlote DESC";

            $resultado = $conn->query($sql);

        ?>

            <thead>

                <tr>

                    <th>Produto</th>

                    <th>Nº Lote</th>

                    <th>Quantidade</th>

                    <th>Validade</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                <?php

                if ($resultado && $resultado->num_rows > 0) {

                    while ($row = $resultado->fetch_assoc()) {

                        $data_validade = ($row['validade']) ? date('d/m/Y', strtotime($row['validade'])) : '-';

                        $status_limpo = strtolower(trim($row['status_lote']));

                        $classe_badge = 'badge-normal';

                       

                        if ($status_limpo == 'vencido') {

                            $classe_badge = 'badge-vencido';

                        } elseif ($status_limpo == 'promocao' || $status_limpo == 'promoção') {

                            $classe_badge = 'badge-promocao';

                        }

                       

                        echo "<tr>

                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>

                                <td style='color: #94a3b8;'>#" . htmlspecialchars($row['numero_lote']) . "</td>

                                <td>" . intval($row['quantidade']) . " un</td>

                                <td>" . $data_validade . "</td>

                                <td><span class='badge " . $classe_badge . "'>" . htmlspecialchars($row['status_lote']) . "</span></td>

                              </tr>";

                    }

                } else {

                    echo "<tr><td colspan='5' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhum dado encontrado para este período.</td></tr>";

                }

                ?>

            </tbody>

        <?php endif; ?>

    </table>

</div>