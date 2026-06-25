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

$data_hoje = date('Y-m-d');
$data_limite_30_dias = date('Y-m-d', strtotime('+30 days'));

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
        SELECT
            nome,
            email,
            tipocadastro
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
                <a href="../cadastro_produtos/cad_list_prods.php">➡️​ Entrada</a>
                <a href="../saida/saida.php"> ⬅️​ Saida</a>
                <a href="../consulta/consulta.php"> 📦 Consulta</a>
                <a href="../compras/compras.php">🛒 Compras</a>
                <a href="../relatorios/buscar_relatorio.php">📊 Relatórios</a>
                <a href="painel_principal_config.php">⚙️ Configurações</a>
            </nav>
            <a href="../../index.html" class="logout">🚪 Sair</a>
        </aside>

        <main class="main">
            <div class="container">

                <h2>Configurações do Sistema</h2>
                <p class="usuario">Usuário logado: <b><?= htmlspecialchars($_SESSION['nome']) ?></b></p>

                <div class="container-abas">
                    <button class="botao-aba ativa" onclick="alternarAba('aba-descontos', this)">🔥 Gerenciar Descontos</button>
                    <button class="botao-aba" onclick="alternarAba('aba-geral', this)">⚙️ Configurações Gerais</button>
                    <button class="botao-aba" onclick="alternarAba('aba-notificacoes', this)">🔔 Alertas e Estoque</button>

                    <!-- parte que separa a config de adm pro funcionario  -->
                    
                  <?php if ($_SESSION['tipoCadastro'] == 'EMPRESA/ADM') { ?>
        <button class="botao-aba" onclick="alternarAba('aba-adm', this)">
            ⚙️ Configurações de ADM
        </button>
    <?php } ?>

    <!-- fim -->

                </div>

                <div id="aba-descontos" class="conteudo-aba ativa">
                    <h3 class="titulo-secao"> Gerenciamento de descontos </h3>
                    
                    <div class="container-sub-modos">
                        <button class="btn-sub-modo ativo" id="btnModoVencimento" onclick="alternarSubModo('vencimento')">⏰ Por Vencimento (Até 30 dias)</button>
                        <button class="btn-sub-modo" id="btnModoManual" onclick="alternarSubModo('manual')">🎯 Lotes Escolhidos Manualmente</button>
                    </div>

                    <div id="modo-vencimento">
                        <h3 class="titulo-secao">Desconto em Lotes Próximos ao Vencimento</h3>
                        <p class="texto-explicativo">Selecione quais lotes críticos receberão o ajuste promocional ou aplique a todos simultaneamente.</p>
                        
                        <form action="gerenciar_descontos.php" method="POST" id="formVencimento">
                            <input type="hidden" name="tipo_acao" value="vencimento_automatico">
                            
                            <div class="barra-ferramentas-lista">
                                <div class="celula-busca">
                                    <input type="text" class="input-filtro-busca" id="buscaVenc" onkeyup="filtrarLotes('buscaVenc', 'listaVenc')" placeholder="Pesquisar lote crítico...">
                                </div>
                                <div class="celula-acoes">
                                    <button type="button" class="btn-marcar-todos" onclick="marcarTodosLotes('listaVenc', this)">☑️ Selecionar Todos</button>
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
                                            <div class="radio-alinhado-label" style="color:#28a745;">Aplicar Desconto Definido</div>
                                        </div>
                                    </label>
                                    <label class="radio-card-cell" for="recNaoVenc">
                                        <div class="radio-conteudo-alinhado">
                                            <div class="radio-alinhado-input">
                                                <input type="radio" name="aplicar_desconto" value="nao" id="recNaoVenc">
                                            </div>
                                            <div class="radio-alinhado-label" style="color:#dc3545;">Zerar Desconto (Preço Normal)</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="campoPorcentagemVenc" class="painel-opcao">
                                <label class="label-destaque">Porcentagem do Desconto (%):</label>
                                <div class="input-porcentagem-container">
                                    <input type="number" name="porcentagem_desconto" id="porcentagemVenc" min="1" max="30" value="10">
                                    <span class="texto-off">% OFF</span>
                                </div>
                            </div>
                            <button type="submit" class="btn-salvar">Executar nos Itens Próximos ao Vencimento</button>
                        </form>
                    </div>

                    <div id="modo-manual" style="display: none;">
                        <h3 class="titulo-secao">Desconto por Escolha Manual de Lotes</h3>
                        <p class="texto-explicativo">Configure descontos comerciais comuns em qualquer lote de estoque (Exclui os lotes em período crítico de vencimento).</p>
                        
                        <form action="gerenciar_descontos.php" method="POST" id="formManual">
                            <input type="hidden" name="tipo_acao" value="escolha_manual">

                            <div class="barra-ferramentas-lista">
                                <div class="celula-busca">
                                    <input type="text" class="input-filtro-busca" id="buscaMan" onkeyup="filtrarLotes('buscaMan', 'listaMan')" placeholder="Filtrar por nome ou marca do produto...">
                                </div>
                                <div class="celula-acoes">
                                    <button type="button" class="btn-marcar-todos" onclick="marcarTodosLotes('listaMan', this)">☑️ Selecionar Todos</button>
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
                                            <div class="radio-alinhado-label" style="color:#28a745;">Aplicar Desconto Definido</div>
                                        </div>
                                    </label>
                                    <label class="radio-card-cell" for="acaoZerar">
                                        <div class="radio-conteudo-alinhado">
                                            <div class="radio-alinhado-input">
                                                <input type="radio" name="aplicar_manual" value="nao" id="acaoZerar">
                                            </div>
                                            <div class="radio-alinhado-label" style="color:#dc3545;">Zerar Desconto (Preço Normal)</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="campoPorcentagemManual" class="painel-opcao">
                                <label class="label-destaque">Porcentagem do Desconto (%):</label>
                                <div class="input-porcentagem-container">
                                    <input type="number" name="porcentagem_manual" id="porcentagemMan" min="1" max="30" value="10">
                                    <span class="texto-off">% OFF</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-salvar">Executar nos Selecionados Manualmente</button>
                        </form>
                    </div>

                </div>

                <div id="aba-geral" class="conteudo-aba">
                    <h3 class="titulo-secao">Dados do Estabelecimento</h3>
                    <div class="painel-opcao"><p style="margin:0; color:#a0aab5; font-style:italic;">Funcionalidade em desenvolvimento.</p></div>
                </div>

                <div id="aba-notificacoes" class="conteudo-aba">
                    <h3 class="titulo-secao">Regras de Estoque Crítico</h3>
                    <div class="painel-opcao"><p style="margin:0; color:#a0aab5; font-style:italic;">Funcionalidade em desenvolvimento.</p></div>
                </div>
            </div>

<!-- Parte que só aparece pro adm -->
<?php if ($_SESSION['tipoCadastro'] == 'EMPRESA/ADM') { ?>

<div id="aba-adm" class="conteudo-aba">

    <h3 class="titulo-secao">Código da Empresa:</h3>

    <p class="texto-explicativo">
        Esse é o código da sua empresa. Passe esse código para seus funcionários durante o cadastro para que eles possam acessar o estoque da empresa.
    </p>

    <div class="painel-opcao" style="text-align:center;">

        <h2 style="
            font-size:32px;
            color:#4CAF50;
            letter-spacing:3px;
            margin:15px 0;
        ">
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
                    <span style="margin-left:auto;">
                        <?= htmlspecialchars($adm['email']) ?>
                    </span>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p style="padding:15px;">
                Nenhum administrador encontrado.
            </p>

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
                    <span style="margin-left:auto;">
                        <?= htmlspecialchars($funcionario['email']) ?>
                    </span>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p style="padding:15px;">
                Nenhum funcionário encontrado.
            </p>

        <?php } ?>

    </div>

</div>

<?php } ?>

                <!-- fim -->

        </main>
    </div>

    <script src="config.js"></script>
</body>
</html>