<?php
// Define o fuso horário correto do Brasil para sumir com o Warning de data
date_default_timezone_set('America/Sao_Paulo');

// Inicia a sessão para identificar a empresa logada
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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório Gerencial - <?php echo strtoupper($tipo); ?></title>
    
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div id="conteudo-relatorio">
        <div class="header">
            <h1>INVEX - SISTEMA DE GESTÃO DE ESTOQUE</h1>
            <p>Relatório Gerencial Automatizado</p>
        </div>

        <div class="meta-info">
            CATEGORIA: <?php echo strtoupper($tipo); ?><br>
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

                $sql = "SELECT l.numero_lote, l.quantidade, l.validade, l.status_lote, p.NomeProduto FROM produtoslotes l INNER JOIN produtos p ON p.idProduto = l.idproduto WHERE l.idEmpresa = $idEmpresa ORDER BY p.NomeProduto ASC";
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
                echo "<thead>
                        <tr>
                            <th>Produto</th>
                            <th>Lote</th>
                            <th class='text-center'>Quantidade</th>
                            <th class='text-center'>Desconto</th>
                        </tr>
                      </thead>
                      <tbody>";

                $sql = "SELECT l.numero_lote, l.quantidade, l.desconto, p.NomeProduto FROM produtoslotes l INNER JOIN produtos p ON p.idProduto = l.idproduto WHERE l.idEmpresa = $idEmpresa AND l.desconto > 0 ORDER BY l.desconto DESC";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".htmlspecialchars($row['NomeProduto'])."</td>
                            <td>".htmlspecialchars($row['numero_lote'])."</td>
                            <td class='text-center'>{$row['quantidade']} un</td>
                            <td class='text-center' style='color: #00B7C3; font-weight: bold;'>{$row['desconto']}% OFF</td>
                          </tr>";
                }
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
            ?>
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            // Elemento que vamos converter em PDF
            const elemento = document.getElementById('conteudo-relatorio');
            
            // Configurações do arquivo de download
            const opcoes = {
                margin:       10,
                filename:     'relatorio_<?php echo $tipo; ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Executa o script que gera o arquivo e fecha a aba logo em seguida
            html2pdf().set(opcoes).from(elemento).save().then(() => {
                // Pequena pausa para garantir o término do download antes de fechar a aba
                setTimeout(() => { window.close(); }, 1500);
            });
        };
    </script>
</body>
</html>