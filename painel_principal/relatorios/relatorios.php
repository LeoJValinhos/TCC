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
    /* --- CONFIGURAÇÃO DO CONTAINER DA TABELA --- */
    .container-tabela {
        width: 100%;
        margin-top: 20px;
        background: #001a36;
        border: 1px solid rgba(0, 245, 212, 0.2);
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        
        /* Limita a tabela a um tamanho fixo seguro para não empurrar a página */
        max-height: 450px !important; 
        overflow-y: auto !important; /* Ativa o scroll apenas aqui dentro */
    }
    
    .tabela-dados {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-family: sans-serif;
    }
    
    /* --- CABEÇALHO FIXO COM COR SÓLIDA --- */
    .tabela-dados th {
        background-color: #001a36 !important; /* Mesma cor de fundo do container */
        color: #00F5D4;
        padding: 14px 18px;
        font-size: 14px;
        text-transform: uppercase;
        border-bottom: 2px solid rgba(0, 245, 212, 0.3);
        
        /* Gruda no topo da tabela ao rolar */
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
    }
    
    .tabela-dados td {
        padding: 12px 18px;
        color: #e2e8f0;
        font-size: 14px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    /* --- BADGES --- */
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
           2. CENÁRIO: RELATÓRIO DE DESCONTOS APLICADOS (LOTES EM PROMOÇÃO)
           ================================================================= */
        elseif ($tipo == "descontos"):
            // Seleciona diretamente os lotes com desconto ativo (> 0), trazendo a validade e status para a verificação
            $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.preco_venda, l.desconto, l.validade, l.status_lote
                    FROM produtoslotes l
                    INNER JOIN produtos p ON l.idproduto = p.IdProduto
                    WHERE l.idEmpresa = '$idEmpresa' AND l.desconto > 0 " . $filtro_lote . "
                    ORDER BY l.desconto DESC, p.NomeProduto ASC";
            $resultado = $conn->query($sql);
        ?>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Nº Lote</th>
                    <th>Qtd em Estoque</th>
                    <th style="text-align: right;">Valor Original</th>
                    <th style="text-align: right;">Val. Unit. c/ Desconto</th>
                    <th style="text-align: center;">Desconto Aplicado</th>
                    <th style="text-align: right;">Valor Total Estoque</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado && $resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        $qtd = intval($row['quantidade']);
                        $preco_original = floatval($row['preco_venda']);
                        $porcentagem_desc = floatval($row['desconto']);
                        
                        // Cálculos baseados nos valores unitários e no estoque atual
                        $preco_com_desconto = $preco_original * (1 - ($porcentagem_desc / 100));
                        $total_desconto_lote = ($preco_original * ($porcentagem_desc / 100)) * $qtd;
                        $valor_total_estoque = $preco_com_desconto * $qtd;
                        
                        $status_limpo = strtolower(trim($row['status_lote']));
                        $lote = !empty($row['numero_lote']) ? '#'.htmlspecialchars($row['numero_lote']) : '-';

                        echo "<tr>
                                <td style='font-weight: 600;'>
                                    " . htmlspecialchars($row['NomeProduto']) . " ";
                                    
                                    // Pequena tag de alerta caso o produto esteja em promoção MAS também esteja VENCIDO
                                    if ($status_limpo == 'vencido') {
                                        echo "<span style='background: #ef4444; color: #ffffff; font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-left: 5px; font-weight: bold; vertical-align: middle;'>VENCIDO</span>";
                                    }
                                    
                        echo "  </td>
                                <td style='color: #94a3b8;'> " . $lote . " </td>
                                <td>" . $qtd . " un</td>
                                <td style='text-align: right; color: #94a3b8;'>R$ " . number_format($preco_original, 2, ',', '.') . "</td>
                                <td style='text-align: right; color: #ffffff; font-weight: 500;'>R$ " . number_format($preco_com_desconto, 2, ',', '.') . "</td>
                                <td style='text-align: center; color: #eab308; font-weight: bold;'>
                                    " . number_format($porcentagem_desc, 0) . "% OFF
                                </td>
                                <td style='text-align: right; color: #22c55e; font-weight: bold;'>R$ " . number_format($valor_total_estoque, 2, ',', '.') . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhum lote com promoção ativa encontrado para o período selecionado.</td></tr>";
                }
                ?>
            </tbody>



        <?php

        
        
        /* =================================================================
           3. CENÁRIO: RELATÓRIO DE LUCRO BRUTO (COM COLUNA DE DESCONTO)
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
                    <th style="text-align: right;">Preço Custo (Un)</th>
                    <th style="text-align: center;">Desconto</th>
                    <th style="text-align: right;">Valor Faturado</th>
                    <th style="text-align: right;">Lucro Estimado</th>
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

                        // Define a cor do lucro: vermelho se for negativo (prejuízo) e verde se positivo
                        $cor_lucro = ($lucro >= 0) ? '#22c55e' : '#ef4444';

                        echo "<tr>
                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>
                                <td>" . $qtd . " un</td>
                                <td>" . $data_venda . "</td>
                                <td style='text-align: right; color: #94a3b8;'>R$ " . number_format($custo_unitario, 2, ',', '.') . "</td>
                                <td style='text-align: center;'>";
                                    if ($porcentagem_desc > 0) {
                                        echo "<span style='color: #eab308; font-weight: bold;'>" . number_format($porcentagem_desc, 0) . "% OFF</span>";
                                    } else {
                                        echo "<span style='color: #64748b;'>-</span>";
                                    }
                        echo "  </td>
                                <td style='text-align: right; color: #38bdf8;'>R$ " . number_format($faturamento_real, 2, ',', '.') . "</td>
                                <td style='text-align: right; color: " . $cor_lucro . "; font-weight: bold;'>R$ " . number_format($lucro, 2, ',', '.') . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhuma venda encontrada para calcular lucros neste período.</td></tr>";
                }
                ?>
            </tbody>



        <?php

        /* =================================================================
           4. CENÁRIO: RELATÓRIO DE PRODUTOS CADASTRADOS (PREÇOS E DESCONTOS)
           ================================================================= */
        elseif ($tipo == "produtos"):
            $sql = "SELECT p.NomeProduto, p.MarcaProduto, l.numero_lote, l.preco_compra, l.preco_venda, l.desconto
                    FROM produtoslotes l
                    INNER JOIN produtos p ON l.idproduto = p.IdProduto
                    WHERE l.idEmpresa = '$idEmpresa' " . $filtro_lote . "
                    ORDER BY p.NomeProduto ASC, l.numero_lote ASC";
            $resultado = $conn->query($sql);
        ?>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th style="text-align: center;">Nº Lote</th>
                    <th>Marca</th>
                    <th style="text-align: right;">Preço Compra</th>
                    <th style="text-align: right;">Preço Venda</th>
                    <th style="text-align: center;">Desconto</th>
                    <th style="text-align: right;">Venda Final</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultado && $resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        $p_compra = floatval($row['preco_compra']);
                        $p_venda_base = floatval($row['preco_venda']);
                        $desc = floatval($row['desconto']);
                        
                        // Preço final calculado com desconto aplicado
                        $p_venda_final = $p_venda_base * (1 - ($desc / 100));
                        $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';
                        $marca = !empty($row['MarcaProduto']) ? htmlspecialchars($row['MarcaProduto']) : '-';

                        echo "<tr>
                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>
                                <td style='color: #94a3b8; text-align: center;'>" . $lote . "</td>
                                <td style='color: #ffffff;'>" . $marca . "</td>
                                <td style='color: #ffffff; text-align: right;'>R$ " . number_format($p_compra, 2, ',', '.') . "</td>
                                <td style='color: #94a3b8; text-align: right;'>R$ " . number_format($p_venda_base, 2, ',', '.') . "</td>
                                <td style='text-align: center;'>";
                                    if ($desc > 0) {
                                        echo "<span style='color: #ef4444; font-weight: bold;'>" . $desc . "%</span>";
                                    } else {
                                        echo "<span style='color: #94a3b8;'>-</span>";
                                    }
                        echo "  </td>
                                <td style='text-align: right; font-weight: bold; color: " . ($desc > 0 ? '#22c55e' : '#e2e8f0') . ";'>
                                    R$ " . number_format($p_venda_final, 2, ',', '.') . "
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhum produto ou lote encontrado para o período selecionado.</td></tr>";
                }
                ?>
            </tbody>

        <?php
        /* =================================================================
           5. CENÁRIO: ABA EXCLUSIVA DE LOTES GERAIS (COM MARCA)
           ================================================================= */
        elseif ($tipo == "lotes"):
            $sql = "SELECT p.NomeProduto, p.MarcaProduto, l.numero_lote, l.quantidade, l.validade, l.status_lote
                    FROM produtoslotes l
                    INNER JOIN produtos p ON l.idproduto = p.IdProduto
                    WHERE l.idEmpresa = '$idEmpresa' " . $filtro_lote . "
                    ORDER BY l.idlote DESC";
            $resultado = $conn->query($sql);
        ?>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Nº Lote</th>
                    <th>Marca</th>
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

                        $marca = !empty($row['MarcaProduto']) ? htmlspecialchars($row['MarcaProduto']) : '-';
                        $lote = !empty($row['numero_lote']) ? '#'.htmlspecialchars($row['numero_lote']) : '-';
                        
                        echo "<tr>
                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>
                                <td style='color: #94a3b8;'>" . $lote . "</td>
                                <td style='color: #ffffff;'>" . $marca . "</td>
                                <td>" . intval($row['quantidade']) . " un</td>
                                <td>" . $data_validade . "</td>
                                <td><span class='badge " . $classe_badge . "'>" . htmlspecialchars($row['status_lote']) . "</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhum lote encontrado para este período.</td></tr>";
                }
                ?>
            </tbody>

        <?php
        /* =================================================================
           6. CENÁRIO: ABA EXCLUSIVA DE VENCIMENTO (Apenas Vencidos)
           ================================================================= */
        else: // Se não for nenhum dos anteriores, assume "vencimento" por padrão de segurança
            $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.validade, l.status_lote
                    FROM produtoslotes l
                    INNER JOIN produtos p ON l.idproduto = p.IdProduto
                    WHERE l.idEmpresa = '$idEmpresa' AND LOWER(l.status_lote) = 'vencido' " . $filtro_lote . "
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
                        $lote = !empty($row['numero_lote']) ? '#'.htmlspecialchars($row['numero_lote']) : '-';
                        
                        echo "<tr>
                                <td style='font-weight: 600;'>" . htmlspecialchars($row['NomeProduto']) . "</td>
                                <td style='color: #94a3b8;'>" . $lote . "</td>
                                <td>" . intval($row['quantidade']) . " un</td>
                                <td>" . $data_validade . "</td>
                                <td><span class='badge badge-vencido'>" . htmlspecialchars($row['status_lote']) . "</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; color:#94a3b8; padding:20px;'>Nenhum produto vencido encontrado para este período.</td></tr>";
                }
                ?>
            </tbody>
        <?php endif; ?>
    </table>
</div>