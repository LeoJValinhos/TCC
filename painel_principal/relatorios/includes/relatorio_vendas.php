
<?php
include '../config_global.php';
include '../config_scripts.php';

$simboloMoeda = $config['simbolo_moeda'];
$casasDecimais = (int)$config['casas_decimais'];
$formatoData = $config['formato_data'];
$codigoMoeda = $config['codigo_moeda'] ?? 'BRL';

$step = "0." . str_repeat("0", max(0, $casasDecimais - 1)) . "1";

if ($casasDecimais == 0) {
    $step = "1";
}

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
$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;

// Corrigido 'p.idProduto' para 'p.IdProduto' para manter o padrão do seu banco
$sql_vendas = "SELECT p.NomeProduto, l.numero_lote, l.preco_venda, l.desconto, s.quantidade_saida, s.data_saida, s.motivo_saida
               FROM saida s
               INNER JOIN produtoslotes l ON s.idlote = l.idlote
               INNER JOIN produtos p ON l.idproduto = p.IdProduto
               WHERE l.idEmpresa = '$idEmpresa' AND LOWER(s.motivo_saida) = 'venda' " . $filtro_venda . "
               ORDER BY s.id_saida DESC";

$resultado = $conn->query($sql_vendas);

if (!$resultado) {
    die("<div style='color:red; padding:20px; background:#fff;'><strong>Erro na Consulta de Vendas:</strong> " . $conn->error . "</div>");
}
?>
<?php include_once '../topo_notificacoes.php'; ?>
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
                <th>Custo Unitário</th>
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
                    
                    if ($desconto_porcentagem > 0) {
                        $preco_unitario_final = $preco_unitario * (1 - ($desconto_porcentagem / 100));
                        $valor_total_original = $preco_unitario * $qtd;
                        $valor_total_final = $preco_unitario_final * $qtd;
                    } else {
                        $preco_unitario_final = $preco_unitario;
                        $valor_total_final = $preco_unitario * $qtd;
                    }
                    
                    echo "<tr>";
                    echo "<td style='font-weight: 600; color: #fff;'>" . htmlspecialchars($row['NomeProduto']) . "</td>";
                    echo "<td style='color: #94a3b8;'>#" . htmlspecialchars($row['numero_lote']) . "</td>";
                    echo "<td style='color: #e2e8f0;'>" . $qtd . " un</td>";
                    
                    echo "<td>";
                    if ($desconto_porcentagem > 0) {
                        echo "<span class='preco-original'>R$ " . number_format($preco_unitario, 2, ',', '.') . "</span>";
                        echo "<span class='preco-final' style='color: #94a3b8;'>R$ " . number_format($preco_unitario_final, 2, ',', '.') . "</span>";
                    } else {
                        echo "<span style='color: #94a3b8;'>R$ " . number_format($preco_unitario, 2, ',', '.') . "</span>";
                    }
                    echo "</td>";
                    
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
                echo "<tr><td colspan='7' style='text-align:center; color:#94a3b8; padding:25px;'>Nenhuma venda registrada neste período.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>