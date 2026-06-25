<?php
date_default_timezone_set('America/Sao_Paulo');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Conexão padrão com o banco
$conn = new mysqli("localhost", "root", "usbw", "databasetcc");
if ($conn->connect_error) { die("Erro de conexão: " . $conn->connect_error); }

// Captura o período selecionado vindo do buscar_relatorio.php
$periodo_atual = isset($GLOBALS['periodo_atual']) ? $GLOBALS['periodo_atual'] : "todos";

// Monta o filtro de data correto para as baixas
$filtro_baixa = "";
if ($periodo_atual == "hoje") {
    $filtro_baixa = " AND DATE(s.data_saida) = CURDATE()"; 
} elseif ($periodo_atual == "semana") {
    $filtro_baixa = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($periodo_atual == "mes") {
    $filtro_baixa = " AND DATE(s.data_saida) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
}

// Busca as saídas cujo motivo NÃO seja 'venda' (trazendo o preço de compra para calcular o prejuízo)
$sql_baixas = "SELECT p.NomeProduto, l.numero_lote, l.preco_compra, s.quantidade_saida, s.data_saida, s.motivo_saida
               FROM saida s
               INNER JOIN produtoslotes l ON s.idlote = l.idlote
               INNER JOIN produtos p ON l.idproduto = p.IdProduto
               WHERE LOWER(s.motivo_saida) <> 'venda' " . $filtro_baixa . "
               ORDER BY s.id_saida DESC";

$resultado = $conn->query($sql_baixas);

if (!$resultado) {
    die("<div style='color:red; padding:20px; background:#fff;'><strong>Erro na Consulta de Baixas:</strong> " . $conn->error . "</div>");
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
    .badge-perda {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        background: rgba(239, 68, 68, 0.1); 
        color: #ef4444; 
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .valor-prejuizo {
        color: #ef4444;
        font-weight: bold;
    }
</style>

<div class="container-tabela">
    <table class="tabela-dados">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Nº Lote</th>
                <th>Qtd Perdida</th>
                <th>Custo Total (Prejuízo)</th>
                <th>Data da Baixa</th>
                <th>Motivo / Ocorrência</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $data_baixa = ($row['data_saida']) ? date('d/m/Y H:i', strtotime($row['data_saida'])) : '-';
                    
                    $qtd = intval($row['quantidade_saida']);
                    $custo_compra = floatval($row['preco_compra']);
                    $prejuizo_total = $custo_compra * $qtd;
                    
                    echo "<tr>";
                    echo "<td style='font-weight: 600; color: #fff;'>" . htmlspecialchars($row['NomeProduto']) . "</td>";
                    echo "<td style='color: #94a3b8;'>#" . htmlspecialchars($row['numero_lote']) . "</td>";
                    echo "<td>" . $qtd . " un</td>";
                    
                    // Coluna de Custo com destaque em Vermelho indicando perda financeira
                    echo "<td><span class='valor-prejuizo'>R$ " . number_format($prejuizo_total, 2, ',', '.') . "</span></td>";
                    
                    echo "<td>" . $data_baixa . "</td>";
                    echo "<td><span class='badge-perda'>" . htmlspecialchars($row['motivo_saida']) . "</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8; padding:25px;'>Nenhuma perda ou baixa registrada neste período.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>