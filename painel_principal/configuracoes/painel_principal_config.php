
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
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION)) {
    session_start();
}
include_once 'alertas.php'; 
include __DIR__ . '/../config_global.php';
include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';




$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;

if (!$idEmpresa) {
    die("Erro: Sessão expirada. Faça login novamente.");
}

$data_hoje = date('Y-m-d');
$data_limite_30_dias = date('Y-m-d', strtotime('+30 days'));

$sqlConfig = "SELECT * FROM configuracoes_gerais WHERE idEmpresa = '$idEmpresa' LIMIT 1";
$resultConfig = mysqli_query($conn, $sqlConfig);

if(mysqli_num_rows($resultConfig) > 0){

    $config = mysqli_fetch_assoc($resultConfig);

}else{

    mysqli_query($conn,"
        INSERT INTO configuracoes_gerais
        (idEmpresa)
        VALUES
        ('$idEmpresa')
    ");

    $resultConfig = mysqli_query($conn, $sqlConfig);

    $config = mysqli_fetch_assoc($resultConfig);

}

/* =========================================================================
   BUSCA 1: APENAS LOTES PRÓXIMOS AO VENCIMENTO (Vencimento <= 30 dias)
   ========================================================================= */
$query_vencimento = "
    SELECT pl.idLote, p.NomeProduto, p.MarcaProduto, pl.validade, pl.quantidade, pl.preco_venda, pl.desconto
    FROM produtoslotes pl
    JOIN produtos p ON pl.idProduto = p.idProduto
    WHERE pl.idEmpresa = $idEmpresa 
      AND pl.quantidade > 0 
      AND pl.validade >= '$data_hoje' 
      AND pl.validade <= '$data_limite_30_dias'
    ORDER BY pl.validade ASC, p.NomeProduto ASC
";
$resultado_vencimento = $conn->query($query_vencimento);

/* =========================================================================
   BUSCA 2: LOTES GERAIS (EXCLUI OS PRÓXIMOS AO VENCIMENTO)
   ========================================================================= */
$query_manual = "
    SELECT pl.idLote, p.NomeProduto, p.MarcaProduto, pl.validade, pl.quantidade, pl.preco_venda, pl.desconto
    FROM produtoslotes pl
    JOIN produtos p ON pl.idProduto = p.idProduto
    WHERE pl.idEmpresa = $idEmpresa 
      AND pl.quantidade > 0 
      AND (pl.validade < '$data_hoje' OR pl.validade > '$data_limite_30_dias')
    ORDER BY p.NomeProduto ASC, pl.validade ASC
";
$resultado_manual = $conn->query($query_manual);

/* =========================================================================
   BUSCA 3: USUÁRIOS DA EMPRESA (SOMENTE ADM)
   ========================================================================= */
$listaAdministradores = [];
$listaFuncionarios = [];

if ($_SESSION['tipoCadastro'] == 'EMPRESA/ADM') {
    $stmt = $conn->prepare("
        SELECT nome, email, tipocadastro
        FROM cadastros
        WHERE idEmpresa = ?
        ORDER BY nome ASC
    ");
    $stmt->bind_param("i", $idEmpresa);
    $stmt->execute();
    $resultadoUsuarios = $stmt->get_result();

    while ($usuario = $resultadoUsuarios->fetch_assoc()) {
        if ($usuario['tipocadastro'] == 'EMPRESA/ADM') {
            $listaAdministradores[] = $usuario;
        } else {
            $listaFuncionarios[] = $usuario;
        }
    }
}
?>
<?php include_once '../topo_notificacoes.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="config.css">
    <link rel="icon" type="image/png" href="../../Imagens/Carrinho.png">
    <title>INVEX - Configurações</title>
</head>

<body>

    <header class="topbar">
        <div class="top-left">
            <img src="../../imagens/carrinho2.png" width="70" height="70" alt="Logo Carrinho">
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
                <a href="../relatorios/buscar_relatorio.php">📊 Relatórios</a>
                <a href="painel_principal_config.php">⚙️ Configurações</a>
            </nav>
            <a href="../../index.html" class="logout">🚪 Sair</a>
        </aside>

        <main class="main">
            <div class="container">

                <h2>Configurações do sistema</h2>
                <p class="usuario">Usuário logado: <b><?= htmlspecialchars($_SESSION['nome']) ?></b></p>

                <div class="container-abas">
                    <button class="botao-aba ativa" onclick="alternarAba('aba-descontos', this)">🔥 Gerenciar descontos</button>
                    <button class="botao-aba" onclick="alternarAba('aba-geral', this)">⚙️ Configurações gerais</button>
                    <button class="botao-aba" onclick="alternarAba('aba-notificacoes', this)">🔔 Alertas de estoque</button>

                    <?php if ($_SESSION['tipoCadastro'] == 'EMPRESA/ADM') { ?>
                        <button class="botao-aba" onclick="alternarAba('aba-adm', this)">⚙️ Configurações de ADM</button>
                    <?php } ?>
                </div>

                <div id="aba-descontos" class="conteudo-aba ativa">
                    
                    <h3 class="titulo-secao"> Gerenciamento de descontos </h3>
                    
                    <div class="container-sub-modos">
                        <button class="btn-sub-modo ativo" id="btnModoVencimento" onclick="alternarSubModo('vencimento')">⏰ Por vencimento (até 30 dias)</button>
                        <button class="btn-sub-modo" id="btnModoManual" onclick="alternarSubModo('manual')">🎯 Lotes escolhidos manualmente</button>
                    </div>

                    <div id="modo-vencimento">
                        <h3 class="titulo-secao">Desconto em lotes próximos ao vencimento</h3>
                        <p class="texto-explicativo">Selecione quais lotes críticos receberão o ajuste promocional ou aplique a todos simultaneamente.</p>
                        
                        <form action="gerenciar_descontos.php" method="POST" id="formVencimento">
                            <input type="hidden" name="tipo_acao" value="vencimento_automatico">
                            
                            <div class="barra-ferramentas-lista">
                                <div class="celula-busca">
                                    <input type="text" class="input-filtro-busca" id="buscaVenc" onkeyup="filtrarLotes('buscaVenc', 'listaVenc')" placeholder="Pesquisar lote crítico...">
                                </div>
                                <div class="celula-acoes">
                                    <button type="button" class="btn-marcar-todos" onclick="marcarTodosLotes('listaVenc', this)">☑️ Selecionar todos</button>
                                </div>
                            </div>

                            <div class="grid-lotes-container" id="listaVenc">
                                <?php if($resultado_vencimento->num_rows > 0) { 
                                        while($lote = $resultado_vencimento->fetch_assoc()) { 
                                            $data_formatada = date('d/m/Y', strtotime($lote['validade']));
                                ?>
                                    <div class="item-lote-linha">
                                        <div class="celula-checkbox">
                                            <input type="checkbox" name="lotes_vencimento[]" value="<?= $lote['idLote'] ?>" id="lote_v_<?= $lote['idLote'] ?>">
                                        </div>
                                        <label class="celula-info-texto" for="lote_v_<?= $lote['idLote'] ?>">
                                            Produto: <strong><?= htmlspecialchars($lote['NomeProduto']) ?> (<?= htmlspecialchars($lote['MarcaProduto']) ?>)</strong> | 
                                            Qtd: <?= $lote['quantidade'] ?> | 
                                            Validade: <span style="color:#ff4d4d; font-weight:bold;"><?= $data_formatada ?> (Crítico)</span> | 
                                            Preço: R$ <?= number_format($lote['preco_venda'], 2, ',', '.') ?>
                                            <?= $lote['desconto'] > 0 ? "<span class='badge-promo'>{$lote['desconto']}% OFF</span>" : "" ?>
                                        </label>
                                    </div>
                                <?php } } else { ?>
                                    <p style="padding:15px; color:#a0aab5; margin:0;">Nenhum lote com validade menor ou igual a 30 dias foi localizado.</p>
                                <?php } ?>
                            </div>

                            <div class="painel-opcao">
                                <label class="label-destaque">Ação para os lotes críticos marcados:</label>
                                <div class="wrapper-radios-tabela">
                                    <label class="radio-card-cell" for="recSimVenc">
                                        <div class="radio-conteudo-alinhado">
                                            <div class="radio-alinhado-input">
                                                <input type="radio" name="aplicar_desconto" value="sim" id="recSimVenc" checked>
                                            </div>
                                            <div class="radio-alinhado-label" style="color:#28a745;">Aplicar desconto definido</div>
                                        </div>
                                    </label>
                                    <label class="radio-card-cell" for="recNaoVenc">
                                        <div class="radio-conteudo-alinhado">
                                            <div class="radio-alinhado-input">
                                                <input type="radio" name="aplicar_desconto" value="nao" id="recNaoVenc">
                                            </div>
                                            <div class="radio-alinhado-label" style="color:#dc3545;">Zerar desconto (preço normal)</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="campoPorcentagemVenc" class="painel-opcao">
                                <label class="label-destaque">Porcentagem do desconto (%):</label>
                                <div class="input-porcentagem-container">
                                    <input type="number" name="porcentagem_desconto" id="porcentagemVenc" min="1" max="30" value="10">
                                    <span class="texto-off">% OFF</span>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                                <button type="submit" class="btn-salvar" style="margin: 0;">Executar nos itens próximos ao vencimento</button>
                                <button type="button" class="btn-marcar-todos" onclick="abrirModalLimite('porcentagemVenc')" style="background-color: #314357; padding: 10px 15px;"> Ajustar limite máximo</button>
                            </div>
                        </form>
                    </div>

                    <div id="modo-manual" style="display: none;">
                        <h3 class="titulo-secao">Desconto por escolha manual de lotes</h3>
                        <p class="texto-explicativo">Configure descontos comerciais comuns em qualquer lote de estoque (exclui os lotes em período crítico de vencimento).</p>
                        
                        <form action="gerenciar_descontos.php" method="POST" id="formManual">
                            <input type="hidden" name="tipo_acao" value="escolha_manual">

                            <div class="barra-ferramentas-lista">
                                <div class="celula-busca">
                                    <input type="text" class="input-filtro-busca" id="buscaMan" onkeyup="filtrarLotes('buscaMan', 'listaMan')" placeholder="Filtrar por nome ou marca do produto...">
                                </div>
                                <div class="celula-acoes">
                                    <button type="button" class="btn-marcar-todos" onclick="marcarTodosLotes('listaMan', this)">☑️ Selecionar todos</button>
                                </div>
                            </div>

                            <div class="grid-lotes-container" id="listaMan">
                                <?php if($resultado_manual->num_rows > 0) { 
                                        while($lote = $resultado_manual->fetch_assoc()) { 
                                            $data_formatada = date('d/m/Y', strtotime($lote['validade']));
                                ?>
                                    <div class="item-lote-linha">
                                        <div class="celula-checkbox">
                                            <input type="checkbox" name="lotes_selecionados[]" value="<?= $lote['idLote'] ?>" id="lote_m_<?= $lote['idLote'] ?>">
                                        </div>
                                        <label class="celula-info-texto" for="lote_m_<?= $lote['idLote'] ?>">
                                            Produto: <strong><?= htmlspecialchars($lote['NomeProduto']) ?> (<?= htmlspecialchars($lote['MarcaProduto']) ?>)</strong> | 
                                            Qtd: <?= $lote['quantidade'] ?> | 
                                            Validade: <?= $data_formatada ?> | 
                                            Preço Original: R$ <?= number_format($lote['preco_venda'], 2, ',', '.') ?>
                                            <?= $lote['desconto'] > 0 ? "<span class='badge-promo'>{$lote['desconto']}% OFF</span>" : "" ?>
                                        </label>
                                    </div>
                                <?php } } else { ?>
                                    <p style="padding:15px; color:#a0aab5; margin:0;">Nenhum lote geral elegível encontrado.</p>
                                <?php } ?>
                            </div>

                            <div class="painel-opcao">
                                <label class="label-destaque">Defina a ação para os lotes marcados acima:</label>
                                <div class="wrapper-radios-tabela">
                                    <label class="radio-card-cell" for="acaoAplicar">
                                        <div class="radio-conteudo-alinhado">
                                            <div class="radio-alinhado-input">
                                                <input type="radio" name="aplicar_manual" value="sim" id="acaoAplicar" checked>
                                            </div>
                                            <div class="radio-alinhado-label" style="color:#28a745;">Aplicar desconto definido</div>
                                        </div>
                                    </label>
                                    <label class="radio-card-cell" for="acaoZerar">
                                        <div class="radio-conteudo-alinhado">
                                            <div class="radio-alinhado-input">
                                                <input type="radio" name="aplicar_manual" value="nao" id="acaoZerar">
                                            </div>
                                            <div class="radio-alinhado-label" style="color:#dc3545;">zerar desconto (Preço normal)</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="campoPorcentagemManual" class="painel-opcao">
                                <label class="label-destaque">Porcentagem do desconto (%):</label>
                                <div class="input-porcentagem-container">
                                    <input type="number" name="porcentagem_manual" id="porcentagemMan" min="1" max="30" value="10">
                                    <span class="texto-off">% OFF</span>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                                <button type="submit" class="btn-salvar" style="margin: 0;">Executar nos selecionados manualmente</button>
                                <button type="button" class="btn-marcar-todos" onclick="abrirModalLimite('porcentagemMan')" style="background-color: #314357; padding: 10px 15px;">Ajustar limite máximo</button>
                            </div>
                        </form>
                    </div>

                    <div id="modalLimiteDesconto" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); align-items: center; justify-content: center;">
                        <div style="background-color: #152232; padding: 25px; border-radius: 8px; border: 1px solid #23354d; width: 320px; text-align: center; box-shadow: 0px 4px 15px rgba(0,0,0,0.5);">
                            <h4 style="color: #fff; margin-top: 0; margin-bottom: 15px; font-size: 18px;">Alterar limite de desconto</h4>
                            <p style="color: #a0aab5; font-size: 13px; margin-bottom: 20px;">Defina o novo limite máximo permitido para digitação nos campos de porcentagem.</p>
                            
                            <div style="margin-bottom: 20px;">
                                <input type="number" id="novoLimiteInput" value="30" min="1" max="100" style="background-color: #0b131e; border: 1px solid #23354d; color: #fff; padding: 10px; width: 80px; text-align: center; font-size: 16px; border-radius: 4px;">
                                <span style="color: #fff; font-weight: bold; margin-left: 5px;">%</span>
                            </div>

                            <div style="display: flex; justify-content: space-around;">
                                <button type="button" onclick="fecharModalLimite()" style="background-color: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Cancelar</button>
                                <button type="button" onclick="salvarNovoLimite()" style="background-color: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Salvar</button>
                            </div>
                        </div>
                    </div>

                    <script>
                    function abrirModalLimite(idCampoOrigem) {
                        var limiteAtual = document.getElementById(idCampoOrigem).getAttribute('max') || 30;
                        document.getElementById('novoLimiteInput').value = limiteAtual;
                        document.getElementById('modalLimiteDesconto').style.display = 'flex';
                    }

                    function fecharModalLimite() {
                        document.getElementById('modalLimiteDesconto').style.display = 'none';
                    }

                    function salvarNovoLimite() {
                        var novoLimite = parseInt(document.getElementById('novoLimiteInput').value);
                        if (isNaN(novoLimite) || novoLimite < 1 || novoLimite > 100) {
                            alert('Por favor, insira um limite válido entre 1 e 100%.');
                            return;
                        }
                        document.getElementById('porcentagemVenc').setAttribute('max', novoLimite);
                        var campoManual = document.getElementById('porcentagemMan');
                        if (campoManual) {
                            campoManual.setAttribute('max', novoLimite);
                        }
                        alert('Limite máximo de desconto alterado para ' + novoLimite + '%!');
                        fecharModalLimite();
                    }
                    </script>
                    </div>
                     <div id="aba-geral" class="conteudo-aba">
                        <form action="salvar_config_gerais.php" method="POST">

<div class="config-page">

    <h2 class="config-title">
        <i class="fa fa-sliders"></i>
        Configurações gerais
    </h2>

    <div class="config-grid">

        <!-- ================= NOTIFICAÇÕES ================= -->

        <div class="config-card">

            <div class="card-header">
                <i class="fa fa-bell"></i>
                <span>Notificações</span>
            </div>

            <p class="card-text">
                Escolha como deseja receber os alertas emitidos pelo sistema INVEX.
            </p>

            <div class="switch-item">

                <div>
                    <strong>Alertas por e-mail</strong>
                    <small>Receber notificações importantes por e-mail.</small>
                </div>

                <label class="switch">
                    <input
                    type="checkbox"
                    name="alerta_email"
                   <?= $config['alerta_email'] ? 'checked' : '' ?>
>
                    <span class="slider"></span>
                </label>

            </div>

            <div class="switch-item">

                <div>
                    <strong>Alertas no login</strong>
                    <small>Mostrar avisos sempre que entrar no sistema.</small>
                </div>

                <label class="switch">
                    <input
    type="checkbox"
    name="alerta_login"
    <?= $config['alerta_login'] ? 'checked' : '' ?>
>
                    <span class="slider"></span>
                </label>

            </div>

            <div class="switch-item">

                <div>
                    <strong>Som dos alertas</strong>
                    <small>Reproduzir um som quando surgir um novo alerta.</small>
                </div>

                <label class="switch">
                    <input
    type="checkbox"
    name="som_alerta"
    <?= $config['som_alerta'] ? 'checked' : '' ?>
>
                    <span class="slider"></span>
                </label>

            </div>

        </div>

        <!-- ================= FORMATAÇÃO ================= -->

        <div class="config-card">

            <div class="card-header">
                <i class="fa fa-file-text"></i>
                <span>Formatação</span>
            </div>

            <div class="campo">

                <label>Casas decimais</label>

                <small>
                    Define quantos números aparecerão após a vírgula em preços, quantidades e valores monetários.
                </small>

                <select name="casas_decimais">
                    <option value="0" <?= $config['casas_decimais']==0?'selected':'' ?>>0</option>
<option value="1" <?= $config['casas_decimais']==1?'selected':'' ?>>1</option>
<option value="2" <?= $config['casas_decimais']==2?'selected':'' ?>>2</option>
<option value="3" <?= $config['casas_decimais']==3?'selected':'' ?>>3</option>
                </select>

            </div>

            <div class="campo">

                <label>Símbolo da moeda</label>

                <small>
                    Símbolo utilizado em todos os valores exibidos pelo sistema.
                </small>

                <select name="simbolo_moeda">
                    <option value="R$" <?= $config['simbolo_moeda']=="R$"?'selected':'' ?>>R$</option>
<option value="$" <?= $config['simbolo_moeda']=="$"?'selected':'' ?>>$</option>
<option value="€" <?= $config['simbolo_moeda']=="€"?'selected':'' ?>>€</option>
                </select>

            </div>

            <div class="campo">

                <label>Formato da data</label>

                <small>
                    Define como as datas serão apresentadas em todas as telas.
                </small>

                <select name="formato_data">
                    <option value="d/m/Y" <?= $config['formato_data']=="d/m/Y"?'selected':'' ?>>DD/MM/AAAA</option>
<option value="m/d/Y" <?= $config['formato_data']=="m/d/Y"?'selected':'' ?>>MM/DD/AAAA</option>
<option value="Y-m-d" <?= $config['formato_data']=="Y-m-d"?'selected':'' ?>>AAAA-MM-DD</option>
                </select>

            </div>

        </div>

        <!-- ================= MANUTENÇÃO ================= -->

        <div class="config-card manutencao-full">

            <div class="card-header">
                <i class="fa fa-cogs"></i>
                <span>Manutenção</span>
            </div>

            <p class="card-text">
                Ferramentas administrativas para manter o sistema funcionando corretamente.
            </p>

            <button class="btn-card">
                <i class="fa fa-trash"></i>
                Limpar logs antigos
            </button>

            <button class="btn-card">
                <i class="fa fa-database"></i>
                Realizar backup
            </button>

            <button class="btn-card">
                <i class="fa fa-refresh"></i>
                Atualizar sistema
            </button>

        </div>

        
        </div>

    </div>

    <div class="save-area">

        <button type="submit" class="save-button">

    <i class="fa fa-save"></i>

    Salvar alterações

</button>

        <p>
            As alterações serão aplicadas imediatamente após serem salvas.
        </p>

    </div>

</div>
</form>
</div>

<style>
.manutencao-full {
grid-column: span 2;
}

/*==================================================
    PÁGINA
==================================================*/

.config-page{

    width:100%;

    min-height:100vh;

    padding:35px;

    box-sizing:border-box;

}

.config-title{

    color:#19f4e7;

    font-size:34px;

    font-weight:700;

    margin-bottom:35px;

    display:flex;

    align-items:center;

    gap:14px;

}

/*==================================================
    GRID
==================================================*/

.config-grid{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:28px;

}

/*==================================================
    CARD
==================================================*/

.config-card{

    background:#0b1724;

    border:1px solid rgba(0,255,255,.18);

    border-radius:16px;

    padding:28px;

    min-height:360px;

    transition:.25s;

}

.config-card:hover{

    transform:translateY(-4px);

    border-color:#18e8e8;

    box-shadow:0 0 18px rgba(0,255,255,.10);

}

/*==================================================
    HEADER CARD
==================================================*/

.card-header{

    display:flex;

    align-items:center;

    gap:12px;

    margin-bottom:18px;

}

.card-header i{

    color:#17f5e7;

    font-size:22px;

}

.card-header span{

    color:#17f5e7;

    font-size:23px;

    font-weight:700;

}

/*==================================================
    TEXTO
==================================================*/

.card-text{

    color:#93a7b6;

    line-height:22px;

    margin-bottom:22px;

    font-size:14px;

}

/*==================================================
    CAMPOS
==================================================*/

.campo{

    margin-bottom:22px;

}

.campo label{

    display:block;

    color:white;

    font-size:15px;

    font-weight:600;

    margin-bottom:6px;

}

.campo small{

    display:block;

    color:#7e97a7;

    font-size:12px;

    line-height:18px;

    margin-bottom:10px;

}

/*==================================================
    SELECT
==================================================*/

.config-card select{

    width:100%;

    height:46px;

    border-radius:8px;

    background:#152638;

    border:1px solid #33506a;

    color:white;

    padding:0 14px;

    font-size:15px;

    outline:none;

    transition:.2s;

}

.config-card select:focus{

    border-color:#19e8e8;

}

/*==================================================
    SWITCH
==================================================*/

.switch-item{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin:20px 0;

    gap:20px;

}

.switch-item strong{

    display:block;

    color:white;

    font-size:15px;

    margin-bottom:4px;

}

.switch-item small{

    color:#87a2b6;

    font-size:12px;

}

/*==================================================
    SWITCH ESTILO IOS
==================================================*/

.switch{

    position:relative;

    display:inline-block;

    width:54px;

    height:28px;

}

.switch input{

    opacity:0;

    width:0;

    height:0;

}

.slider{

    position:absolute;

    cursor:pointer;

    top:0;

    left:0;

    right:0;

    bottom:0;

    background:#344a5f;

    border-radius:50px;

    transition:.25s;

}

.slider:before{

    position:absolute;

    content:"";

    width:22px;

    height:22px;

    left:3px;

    top:3px;

    background:white;

    border-radius:50%;

    transition:.25s;

}

.switch input:checked + .slider{

    background:#19dede;

}

.switch input:checked + .slider:before{

    transform:translateX(26px);

}

/*==================================================
    BOTÕES
==================================================*/

.btn-card{

    width:100%;

    height:48px;

    margin-top:15px;

    background:#162a3c;

    border:1px solid rgba(0,255,255,.18);

    color:#19eeee;

    border-radius:8px;

    cursor:pointer;

    font-size:15px;

    font-weight:600;

    transition:.2s;

}

.btn-card i{

    margin-right:10px;

}

.btn-card:hover{

    background:#1b3146;

    border-color:#19f4e7;

}

/*==================================================
    COLOR
==================================================*/

.config-card input[type=color]{

    width:100%;

    height:48px;

    border:none;

    cursor:pointer;

    background:none;

}

/*==================================================
    SALVAR
==================================================*/

.save-area{

    display:flex;

    flex-direction:column;

    align-items:center;

    margin-top:40px;

}

.save-button{

    background:#1cd8d8;

    color:#001521;

    border:none;

    border-radius:10px;

    padding:15px 50px;

    font-size:17px;

    font-weight:700;

    cursor:pointer;

    transition:.2s;

}

.save-button i{

    margin-right:10px;

}

.save-button:hover{

    background:#27ecec;

    transform:translateY(-2px);

}

.save-area p{

    margin-top:14px;

    color:#8ca4b5;

    font-size:13px;

}

/*==================================================
    RESPONSIVO
==================================================*/

@media(max-width:1100px){

.config-grid{

grid-template-columns:1fr;

}

.config-card{

min-height:auto;

}

}

</style>


<div id="aba-notificacoes" class="conteudo-aba">

    <h3 class="titulo-secao">🔔 Central de alertas e notificações</h3>
    <p class="texto-explicativo">Acompanhe lotes vencidos, itens próximos do vencimento e níveis críticos de estoque.</p>

    <div style="background: #111b27; padding: 15px; border-radius: 6px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <span style="color: #a0aab5; font-size: 13px; font-weight: 500;">⚙️ Ações rápidas do sistema:</span>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" onclick="abrirModalEstoqueGlobal()" style="background-color: #314357; padding: 8px 16px; font-size: 13px; margin: 0; border: none; color: #fff; border-radius: 4px; cursor: pointer;">
                📦 Aplicar estoque mínimo padrão
            </button>
            <button type="button" onclick="abrirModalRestaurar()" style="background-color: #573136; padding: 8px 16px; font-size: 13px; margin: 0; border: none; color: #ff9999; border-radius: 4px; cursor: pointer;">
                ↩️ Voltar ao original de cada produto
            </button>
            <button type="button" onclick="abrirModalApagarTudo()" style="background-color: #721c24; padding: 8px 16px; font-size: 13px; margin: 0; border: none; color: #f8d7da; border-radius: 4px; cursor: pointer;">
                🗑️ Apagar todas as notificações
            </button>
        </div>
    </div>

    <div class="barra-ferramentas-lista" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; background: #111b27; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
        <div style="flex: 1; min-width: 200px;">
            <input type="text" class="input-filtro-busca" id="buscaAlerta" onkeyup="filtrarAlertas()" placeholder="Pesquisar por produto..." style="width: 100%; margin: 0;">
        </div>
        <div style="display: flex; gap: 10px; min-width: 280px;">
            <select id="filtroTipoAlerta" onchange="filtrarAlertas()" style="background: #0b131e; border: 1px solid #23354d; color: #fff; padding: 8px; border-radius: 4px; flex: 1;">
                <option value="todos">Todos os status</option>
                <option value="vencido">Itens vencidos / próximos</option>
                <option value="critico">Estoque crítico / baixo</option>
            </select>
            <select id="ordenacaoAlerta" onchange="ordenarAlertas()" style="background: #0b131e; border: 1px solid #23354d; color: #fff; padding: 8px; border-radius: 4px; flex: 1;">
                <option value="asc">Qtd: Menor primeiro</option>
                <option value="desc">Qtd: Maior primeiro</option>
            </select>
        </div>
    </div>

    <div class="grid-lotes-container" id="listaAlertasContainer">
        <?php if (!empty($alertas_vencidos)): ?>
            <?php foreach($alertas_vencidos as $vencido): ?>
                <div class="item-lote-linha card-alerta" data-tipo="vencido" data-qtd="<?= $vencido['quantidade'] ?>" style="border-left: 5px solid #dc3545; display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: #2c1619; margin-bottom: 8px;">
                    <div class="celula-info-texto" style="margin: 0;">
                        <span class="badge-promo" style="background-color: #dc3545; color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-right: 8px;">VENCIDO</span>
                        Produto: <strong><?= htmlspecialchars($vencido['NomeProduto']) ?></strong> | Lote: <?= htmlspecialchars($vencido['numero_lote'] ?? '') ?> - <span style="color:#ff4d4d; font-weight:bold;">Venceu em <?= date('d/m/Y', strtotime($vencido['validade'])) ?></span> (<?= $vencido['quantidade'] ?> un)
                    </div>
                    <button type="button" class="btn-apagar-alerta" onclick="prepararExclusaoAlerta(<?= $vencido['idproduto'] ?>, '<?= $vencido['numero_lote'] ?? '' ?>', 'vencimento', this)" style="background: none; border: none; color: #ff4d4d; cursor: pointer;">🗑️</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($alertas_proximos)): ?>
            <?php foreach($alertas_proximos as $prox): ?>
                <div class="item-lote-linha card-alerta" data-tipo="vencido" data-qtd="<?= $prox['quantidade'] ?>" style="border-left: 5px solid #ffae42; display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: #2c2516; margin-bottom: 8px;">
                    <div class="celula-info-texto" style="margin: 0;">
                        <span class="badge-promo" style="background-color: #ffae42; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-right: 8px;">VENCENDO LOGO</span>
                        Produto: <strong><?= htmlspecialchars($prox['NomeProduto']) ?></strong> | Lote: <?= htmlspecialchars($prox['numero_lote'] ?? '') ?> - <span style="color:#ffae42; font-weight:bold;">Vence em <?= date('d/m/Y', strtotime($prox['validade'])) ?></span> (Faltam menos de <?= $dias_antecedencia ?> dias!)
                    </div>
                    <button type="button" class="btn-apagar-alerta" onclick="prepararExclusaoAlerta(<?= $prox['idproduto'] ?>, '<?= $prox['numero_lote'] ?? '' ?>', 'vencimento', this)" style="background: none; border: none; color: #ffae42; cursor: pointer;">🗑️</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

      <?php if (!empty($alertas_estoque_baixo)): ?>
    <?php foreach($alertas_estoque_baixo as $prod_baixo): ?>
        <?php 
            // Lógica que diferencia o visual
            $isZerado = ($prod_baixo['total_estoque'] <= 0); 
            $cor = $isZerado ? "#808080" : "#ffc107"; // Vermelho se 0, Amarelo se Baixo
            $bg = $isZerado ? "#3c3c3c" : "#2c2214";
            $badgeText = $isZerado ? "ESGOTADO" : "ESTOQUE BAIXO";
        ?>
        <div class="item-lote-linha card-alerta" data-tipo="estoque" data-qtd="<?= $prod_baixo['total_estoque'] ?>" style="border-left: 5px solid <?= $cor ?>; display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: <?= $bg ?>; margin-bottom: 8px;">
            <div class="celula-info-texto" style="margin: 0;">
                <span style="background-color: <?= $cor ?>; color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-right: 8px;"><?= $badgeText ?></span>
                Produto: <strong><?= htmlspecialchars($prod_baixo['NomeProduto']) ?></strong> | 
                Qtd Atual: <strong style="color:<?= $cor ?>;"><?= $prod_baixo['total_estoque'] ?> un</strong>
            </div>
            <button type="button" class="btn-apagar-alerta" onclick="prepararExclusaoAlerta(<?= $prod_baixo['idProduto'] ?>, '', 'estoque', this)" style="background: none; border: none; color: #ff4d4d; cursor: pointer;">🗑️</button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

        <?php if(empty($alertas_vencidos) && empty($alertas_proximos) && empty($alertas_estoque_baixo)): ?>
            <p style="padding:15px; color:#a0aab5; margin:0; font-style:italic;">🎉 Tudo limpo por aqui!</p>
        <?php endif; ?>
    </div>

    <div style="margin-top: 35px; padding-top: 20px; border-top: 1px solid #23354d;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h4 style="color: #fff; margin: 0;">📜 Histórico de alertas apagados nesta sessão</h4>
            <button type="button" onclick="abrirModalLimparHistorico()" style="background: #23354d; color: #ff9999; border: 1px solid #573136; padding: 5px 12px; border-radius: 4px; font-size: 12px; cursor: pointer;">
                Limpar histórico completo
            </button>
        </div>
        <div id="listaHistoricoContainer" style="max-height: 250px; overflow-y: auto; background: #0b131e; padding: 10px; border-radius: 6px; border: 1px solid #23354d;"></div>
    </div>

    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #23354d;">
        <h4 style="color: #fff; margin-bottom: 10px;">⚙️ Configurar regras de notificação</h4>
        <form action="processar_config_alertas.php" method="POST" style="background: #111b27; padding: 20px; border-radius: 6px; display: flex; gap: 15px; align-items: flex-end; justify-content: space-between;">
            <div>
                <label class="label-destaque" style="display:block; margin-bottom:5px;">Aviso de vencimento antecipado:</label>
                <input type="number" name="dias_vencimento" value="<?= $dias_antecedencia ?>" min="1" style="background:#0b131e; border:1px solid #23354d; color:#fff; padding:8px; border-radius:4px; width:80px; text-align:center;">
                <span style="color:#a0aab5; font-size:13px; margin-left: 5px;">dias de antecedência</span>
            </div>
            <button type="submit" class="btn-salvar" style="margin: 0;">Salvar parâmetros</button>
        </form>
    </div>

    <div id="customModalEstoque" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.75); align-items: center; justify-content: center;">
        <div style="background: #111b27; border: 1px solid #23354d; padding: 25px; border-radius: 8px; width: 100%; max-width: 420px;">
            <h4 style="color: #fff; margin-top: 0;">📦 Definir estoque mínimo global</h4>
            <p style="color: #a0aab5; font-size: 13px;">Digite o valor limite padrão para todos os itens cadastrados:</p>
            <input type="number" id="inputQtdGlobalModal" value="10" min="0" style="width: 100%; background: #0b131e; border: 1px solid #23354d; color: #fff; padding: 10px; border-radius: 4px; margin: 15px 0; text-align: center;">
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="fecharModalEstoqueGlobal()" style="background: #23354d; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="button" onclick="confirmarEstoqueGlobal()" style="background: #314357; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Aplicar</button>
            </div>
        </div>
    </div>

    <div id="customModalRestaurar" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.75); align-items: center; justify-content: center;">
        <div style="background: #111b27; border: 1px solid #23354d; padding: 25px; border-radius: 8px; width: 100%; max-width: 420px;">
            <h4 style="color: #ff9999; margin-top: 0;">↩️ Restaurar estoques mínimos</h4>
            <p style="color: #a0aab5; font-size: 14px;">Deseja retornar aos limites originais individuais de cada item?</p>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="fecharModalRestaurar()" style="background: #23354d; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="button" onclick="confirmarRestauracao()" style="background: #dc3545; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Sim, restaurar</button>
            </div>
        </div>
    </div>

    <div id="customModalConfirmacaoUnica" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.75); align-items: center; justify-content: center;">
        <div style="background: #111b27; border: 1px solid #23354d; padding: 25px; border-radius: 8px; width: 100%; max-width: 420px;">
            <h4 style="color: #fff; margin-top: 0;">🗑️ Remover notificação</h4>
            <p style="color: #a0aab5; font-size: 14px; line-height: 1.5;">Tem certeza que deseja ocultar esta notificação? Ela será enviada para o histórico.</p>
            <div style="margin: 15px 0; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" id="checkNaoPerguntar" style="cursor: pointer; width: 16px; height: 16px;">
                <label for="checkNaoPerguntar" style="color: #a0aab5; font-size: 13px; cursor: pointer; user-select: none;">Não perguntar novamente (Exclusão rápida)</label>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="fecharModalConfirmacaoUnica()" style="background: #23354d; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="button" onclick="confirmarExclusaoAlertaUnico()" style="background: #dc3545; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="customModalApagarTudo" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.75); align-items: center; justify-content: center;">
        <div style="background: #111b27; border: 1px solid #23354d; padding: 25px; border-radius: 8px; width: 100%; max-width: 420px;">
            <h4 style="color: #ff9999; margin-top: 0;">⚠️ Apagar todas as notificações</h4>
            <p style="color: #a0aab5; font-size: 14px;">Tem certeza que deseja apagar todas as notificações ativas na tela de uma só vez? Todas irão para o histórico.</p>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="fecharModalApagarTudo()" style="background: #23354d; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button type="button" onclick="confirmarApagarTudo()" style="background: #dc3545; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Sim, apagar tudo</button>
            </div>
        </div>
    </div>

    <div id="customModalLimparHistorico" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.75); align-items: center; justify-content: center;">
        <div style="background: #111b27; border: 1px solid #23354d; padding: 25px; border-radius: 8px; width: 100%; max-width: 420px;">
            <h4 style="color: #ff4d4d; margin-top: 0;">🚨 Esvaziar histórico permanentemente</h4>
            <p style="color: #a0aab5; font-size: 14px;">Esta ação vai deletar definitivamente os registros de alertas excluídos. Tem certeza?</p>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" onclick="fecharModalLimparHistorico()" style="background: #23354d; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Manter histórico</button>
                <button type="button" onclick="confirmarLimparHistorico()" style="background: #dc3545; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Sim, limpar histórico</button>
            </div>
        </div>
    </div>
</div>
</div>

                <?php if ($_SESSION['tipoCadastro'] == 'EMPRESA/ADM') { ?>
                    <div id="aba-adm" class="conteudo-aba">
                        <h3 class="titulo-secao">Código da Empresa:</h3>
                        <p class="texto-explicativo">
                            Esse é o código da sua empresa. Passe esse código para seus funcionários durante o cadastro para que eles possam acessar o estoque da empresa.
                        </p>
                        <div class="painel-opcao" style="text-align:center;">
                            <h2 style="font-size:32px; color:#4CAF50; letter-spacing:3px; margin:15px 0;">
                                <?= htmlspecialchars($_SESSION['codigoEmpresa']) ?>
                            </h2>
                        </div>

                        <br>
                        <h3 class="titulo-secao">Lista de administradores:</h3>
                        <div class="grid-lotes-container">
                            <?php if (count($listaAdministradores) > 0) { ?>
                                <?php foreach ($listaAdministradores as $adm) { ?>
                                    <div class="item-lote-linha">
                                        <h3 class="titulo-secao"> Nome: </h3>
                                        <strong><?= htmlspecialchars($adm['nome']) ?></strong>
                                        <h3 class="titulo-secao"> E-mail: </h3>
                                        <span style="margin-left:auto;"><?= htmlspecialchars($adm['email']) ?></span>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <p style="padding:15px;">Nenhum administrador encontrado.</p>
                            <?php } ?>
                        </div>

                        <br>
                        <h3 class="titulo-secao">Lista de funcionários:</h3>
                        <div class="grid-lotes-container">
                            <?php if (count($listaFuncionarios) > 0) { ?>
                                <?php foreach ($listaFuncionarios as $funcionario) { ?>
                                    <div class="item-lote-linha">
                                        <h3 class="titulo-secao"> Nome: </h3>
                                        <strong><?= htmlspecialchars($funcionario['nome']) ?></strong>
                                        <h3 class="titulo-secao"> E-mail: </h3>
                                        <span style="margin-left:auto;"><?= htmlspecialchars($funcionario['email']) ?></span>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <p style="padding:15px;">Nenhum funcionario encontrado.</p>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

            </div> </main>
    </div> <script src="config.js"></script>

    
<script>
/* =========================================================================
   SCRIPT DE FILTROS E BUSCA DINÂMICA
   ========================================================================= */
function filtrarAlertas() {
    const textoBusca = document.getElementById('buscaAlerta').value.toLowerCase();
    const tipoFiltro = document.getElementById('filtroTipoAlerta').value;
    const cards = document.querySelectorAll('#listaAlertasContainer .card-alerta');
    cards.forEach(card => {
        const textoCard = card.innerText.toLowerCase();
        const tipoCard = card.getAttribute('data-tipo');
        if (textoCard.includes(textoBusca) && (tipoFiltro === 'todos' || tipoCard === tipoFiltro)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function ordenarAlertas() {
    const container = document.getElementById('listaAlertasContainer');
    const cards = Array.from(container.querySelectorAll('.card-alerta'));
    const ordem = document.getElementById('ordenacaoAlerta').value;
    cards.sort((a, b) => {
        const qtdA = parseInt(a.getAttribute('data-qtd')) || 0;
        const qtdB = parseInt(b.getAttribute('data-qtd')) || 0;
        return ordem === 'asc' ? qtdA - qtdB : qtdB - qtdA;
    });
    cards.forEach(card => container.appendChild(card));
}

/* =========================================================================
   LÓGICA DE EXCLUSÃO E HISTÓRICO VIA BANCO E LOCALSTORAGE
   ========================================================================= */
let elementoParaRemover = null;
let dadosAlertaParaOcultar = {};

document.addEventListener("DOMContentLoaded", function() {
    renderizarHistoricoDOM();
});

function prepararExclusaoAlerta(idProduto, lote, tipo, botao) {
    elementoParaRemover = botao.closest('.card-alerta');
    dadosAlertaParaOcultar = { idProduto: idProduto, numero_lote: lote, tipo: tipo };
    
    const pularConfirmacao = localStorage.getItem('naoPerguntarAlerta') === 'true';

    if (pularConfirmacao) {
        confirmarExclusaoAlertaUnico();
    } else {
        document.getElementById('checkNaoPerguntar').checked = false;
        document.getElementById('customModalConfirmacaoUnica').style.display = 'flex';
    }
}

function fecharModalConfirmacaoUnica() {
    document.getElementById('customModalConfirmacaoUnica').style.display = 'none';
    elementoParaRemover = null;
    dadosAlertaParaOcultar = {};
}

function confirmarExclusaoAlertaUnico() {
    if (!elementoParaRemover) return;

    if (document.getElementById('customModalConfirmacaoUnica').style.display === 'flex') {
        if (document.getElementById('checkNaoPerguntar').checked) {
            localStorage.setItem('naoPerguntarAlerta', 'true');
        }
    }

    let formData = new FormData();
    const idProd = dadosAlertaParaOcultar.idProduto || dadosAlertaParaOcultar.id_produto || '';
    const loteProd = dadosAlertaParaOcultar.numero_lote !== undefined ? dadosAlertaParaOcultar.numero_lote : '';
    const tipoAlerta = dadosAlertaParaOcultar.tipo || '';

    formData.append('idProduto', idProd);
    formData.append('numero_lote', loteProd);
    formData.append('tipo', tipoAlerta);

    const urlDestino = window.location.origin + '/painel_principal/configuracoes/ocultar_alertas_ajax.php';

    fetch(urlDestino, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error(`Status ${response.status}`);
        return response.text(); // Lê como texto puro igual o PHP mandou
    })
    .then(texto => {
        // Remove espaços em branco nas pontas para garantir o match perfeito
        if (texto.trim() === "OK_SUCESSO") {
            const textoInfo = elementoParaRemover.querySelector('.celula-info-texto').innerHTML;
            const tipoCard = elementoParaRemover.getAttribute('data-tipo');
            
            salvarNoHistoricoStorage(textoInfo, tipoCard);
            elementoParaRemover.remove(); // Remove o card da tela na hora!
            
            if (typeof checarSeVazio === 'function') checarSeVazio();
        } else {
            alert('Erro no processamento: ' + texto);
        }
        fecharModalConfirmacaoUnica();
    })
    .catch((error) => {
        console.error('Erro:', error);
        alert('Erro crítico de comunicação com o servidor.');
        fecharModalConfirmacaoUnica();
    });
}
function abrirModalApagarTudo() {
    document.getElementById('customModalApagarTudo').style.display = 'flex';
}

function fecharModalApagarTudo() {
    document.getElementById('customModalApagarTudo').style.display = 'none';
}

function confirmarApagarTudo() {
    const cards = document.querySelectorAll('#listaAlertasContainer .card-alerta');
    
    // Tratando as exclusões sequencialmente no banco
    cards.forEach(card => {
        const botao = card.querySelector('.btn-apagar-alerta');
        if (botao) {
            // Pegamos os atributos do onclick do botão nativo para replicar a exclusão
            const onclickAttr = botao.getAttribute('onclick');
            if (onclickAttr) {
                // Executa a lógica de envio direto ignorando o modal
                const match = onclickAttr.match(/prepararExclusaoAlerta\(([^)]+)\)/);
                if (match) {
                    const params = match[1].split(',').map(p => p.trim().replace(/['"]/g, ""));
                    
                    let formData = new FormData();
                    formData.append('idProduto', params[0]);
                    formData.append('numero_lote', params[1]);
                    formData.append('tipo', params[2]);

                    fetch('../configuracoes/ocultar_alertas_ajax.php', {
                        method: 'POST',
                        body: formData
                    });
                }
            }
        }
        
        const textoInfo = card.querySelector('.celula-info-texto').innerHTML;
        const tipoCard = card.getAttribute('data-tipo');
        salvarNoHistoricoStorage(textoInfo, tipoCard);
        card.remove();
    });

    fecharModalApagarTudo();
    checarSeVazio();
}

function salvarNoHistoricoStorage(htmlConteudo, tipo) {
    let historico = JSON.parse(localStorage.getItem('historicoNotificacoes')) || [];
    const dataHora = new Date().toLocaleString('pt-BR');
    
    historico.unshift({
        conteudo: htmlConteudo,
        tipo: tipo,
        apagadoEm: dataHora
    });

    localStorage.setItem('historicoNotificacoes', JSON.stringify(historico));
    renderizarHistoricoDOM();
}

function renderizarHistoricoDOM() {
    const containerHistorico = document.getElementById('listaHistoricoContainer');
    if (!containerHistorico) return;

    let historico = JSON.parse(localStorage.getItem('historicoNotificacoes')) || [];
    
    if (historico.length === 0) {
        containerHistorico.innerHTML = '<p style="padding:15px; color:#a0aab5; margin:0; font-style:italic;">O histórico está limpo.</p>';
        return;
    }

    containerHistorico.innerHTML = '';
    historico.forEach(item => {
        const div = document.createElement('div');
        div.className = 'item-lote-linha';
        div.style = "border-left: 5px solid #4a5a6a; display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: #18222e; margin-bottom: 8px; border-radius: 4px;";
        
        div.innerHTML = `
            <div style="margin: 0; color: #ced4da; font-size: 13px;">
                ${item.conteudo}
                <br><small style="color: #6c757d; font-size: 11px;">🗑️ Apagado em: ${item.apagadoEm}</small>
            </div>
        `;
        containerHistorico.appendChild(div);
    });
}

function abrirModalLimparHistorico() {
    document.getElementById('customModalLimparHistorico').style.display = 'flex';
}

function fecharModalLimparHistorico() {
    document.getElementById('customModalLimparHistorico').style.display = 'none';
}

function confirmarLimparHistorico() {
    localStorage.removeItem('historicoNotificacoes');
    renderizarHistoricoDOM();
    fecharModalLimparHistorico();
}

function checarSeVazio() {
    const container = document.getElementById('listaAlertasContainer');
    const cards = container.querySelectorAll('.card-alerta');
    if (cards.length === 0) {
        container.innerHTML = '<p style="padding:15px; color:#a0aab5; margin:0; font-style:italic;">🎉 Tudo limpo por aqui!</p>';
    }
}

/* =========================================================================
   CONTROLE DOS MODAIS DE CONFIGURAÇÃO DE ESTOQUE
   ========================================================================= */
function abrirModalEstoqueGlobal() { document.getElementById('customModalEstoque').style.display = 'flex'; }
function fecharModalEstoqueGlobal() { document.getElementById('customModalEstoque').style.display = 'none'; }
function confirmarEstoqueGlobal() {
    var valor = document.getElementById('inputQtdGlobalModal').value;
    if (valor !== "" && parseInt(valor) >= 0) {
        window.location.href = 'processar_config_alertas.php?action=estoque_global&quantidade=' + parseInt(valor);
    } else {
        alert("Insira um número válido maior ou igual a zero.");
    }
}

function abrirModalRestaurar() { document.getElementById('customModalRestaurar').style.display = 'flex'; }
function fecharModalRestaurar() { document.getElementById('customModalRestaurar').style.display = 'none'; }
function confirmarRestauracao() { window.location.href = 'processar_config_alertas.php?action=restaurar_original'; }
</script>
</body>
</html>