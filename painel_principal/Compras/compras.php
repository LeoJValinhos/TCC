<?php
include '../config_global.php';
include '../config_scripts.php';
require_once("produtos_simulado.php");

$simboloMoeda = $config['simbolo_moeda'];
$casasDecimais = (int)$config['casas_decimais'];
$formatoData = $config['formato_data'];
$codigoMoeda = $config['codigo_moeda'] ?? 'BRL';

$step = "0." . str_repeat("0", max(0, $casasDecimais - 1)) . "1";

if ($casasDecimais == 0) {
    $step = "1";
}
?>

<?php include_once '../topo_notificacoes.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>INVEX - Compras</title>
    <link rel="stylesheet" href="compras.css">
    <link rel="icon" type="image/png" href="../../Imagens/Carrinho.png" width="70" height="70">
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
                <a href="compras.php">🛒 Compras</a>
                <a href="../relatorios/buscar_relatorio.php">📊 Relatórios</a>
                <a href="../configuracoes/painel_principal_config.php">⚙️ Configurações</a>
            </nav>

            <a href="../../index.html" class="logout">🚪 Sair</a>
        </aside>

        <main class="main">

            <h1 class="nome-aba">Compras coletivas</h1>

            <div class="produtos">

                <?php foreach($produtos as $produto){ ?>

                    <div class="card">

                        <img src="<?= $produto['imagemProduto'] ?>" alt="<?= $produto['nomeProduto'] ?>">

                        <h3><?= $produto['nomeProduto'] ?></h3>

                        <p class="preco"><?= $simboloMoeda ?> <?= number_format($produto['valor_unitario'], $casasDecimais, ',', '.') ?></p>
                        
                        <p style="font-size: 11px; color: var(--sub); margin-bottom: 10px;">Fornecedor: <?= htmlspecialchars($produto['fornecedor']) ?></p>

                        <p>Quantidade disponível: <?= $produto['quantidade'] ?></p>

                        <p>Participantes: <?= $produto['quantidadeParticipantes'] ?> / <?= $produto['meta'] ?></p>

                        <div class="barra">
                            <div class="progresso" style="width: <?= ($produto['quantidadeParticipantes'] / $produto['meta']) * 100 ?>%;"></div>
                        </div>

                        <br>

                        <?php if($produto['status'] == 'Aberta'){ ?>
                            <span class="status-aberta">Aberta</span>
                        <?php } elseif($produto['status'] == 'Aguardando outro participante'){ ?>
                            <span class="status-aberta">Aguardando outro participante</span>
                        <?php } elseif($produto['status'] == 'Concluida'){ ?>
                            <span class="status-fechada">Concluída</span>
                        <?php } else { ?>
                            <span class="status-fechada">Cancelada</span>
                        <?php } ?>

                        <br><br>

                       <button class="btn-detalhes" onclick="abrirModal(<?= $produto['idItem'] ?>)">Ver Detalhes</button>

                    </div>

                <?php } ?>

            </div>

        </main>

    </div>

    <div id="modalCompra" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="fechar-modal" onclick="fecharModal()">&times;</span>
            
            <div class="modal-img">
                <img id="modalImagem" src="" alt="Produto">
            </div>
            
            <div class="modal-info">
                <h2 id="modalTitulo">Carregando...</h2>
                <p id="modalMarca"></p>
                <p id="modalDescricao"></p>
                <br>
                
                <div class="modal-precos">
                    <p><strong>🏭 Fornecedor:</strong> <span id="modalFornecedor" style="color: #00F5D4;">-</span></p>
                    <p><strong>📦 Valor Total do Lote:</strong> <span id="modalPrecoTotal">-</span></p>
                    <p><strong>🏷️ Valor Unitário:</strong> <span id="modalPrecoUnitario">-</span></p>

                    <div id="modalDescontoInfo" style="display: none; margin-top: 15px; padding: 10px; background: rgba(255, 174, 66, 0.1); border-left: 3px solid #ffae42; border-radius: 5px;">
                        <span id="textoDesconto" style="font-size: 14px;"></span>
                    </div>

                    <hr style="border: 1px solid #00B7C3; margin: 15px 0; opacity: 0.3;">

                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <label for="qtdComprar"><strong>Quantas unidades você deseja?</strong></label>
                        <input type="number" id="qtdComprar" min="1" value="1" style="width: 80px; padding: 8px; border-radius: 5px; border: 1px solid #00B7C3; background: #02152E; color: white; font-size: 16px; text-align: center;">
                    </div>

                    <p style="font-size: 1.3em;">
                        <strong>Custo Estimado:</strong> <span id="modalCustoCalculado" style="color: #00F5D4; font-weight: bold;">-</span>
                    </p>
                </div>
                
                <br>
                <h3>Participantes (<span id="modalQtdPart">0</span>/2):</h3>
                <ul class="participantes-lista" id="listaParticipantes">
                </ul>
                
                <div class="modal-acoes">
                    <button id="btnParticiparModal" class="btn-participar">Participar</button>
                    <button id="btnCancelarModal" class="btn-cancelar">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
const configGeral = {
    simboloMoeda: "<?= addslashes($simboloMoeda) ?>",
    casasDecimais: <?= $casasDecimais ?>,
    formatoData: "<?= $formatoData ?>",
    codigoMoeda: "<?= $codigoMoeda ?>"
};

console.log(configGeral);
</script>
    <script src="compras.js"></script>

</body>
</html>