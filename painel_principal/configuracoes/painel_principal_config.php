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
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../cadastro_produtos/cad_list_prods.css">
    <link rel="icon" type="image/png" href="../../Imagens/Carrinho.png">
    <title>INVEX - Configurações</title>

    <style>
        /* =================================================================
        INTERRUPÇÃO DE QUEBRAS INDESEJADAS & EXPANSÃO VISUAL PREMIUM
        ================================================================== */
        
        .container-abas {
            display: flex;
            gap: 12px;
            border-bottom: 2px solid #002b5c;
            padding-bottom: 14px;
            margin-bottom: 30px;
            margin-top: 20px;
        }

        .botao-aba {
            background-color: #001A36;
            color: #ffffff;
            border: 1px solid #002b5c;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .botao-aba.ativa {
            background-color: #00B7C3;
            color: #000c19;
            border-color: #00B7C3;
            box-shadow: 0 0 12px rgba(0, 183, 195, 0.3);
        }

        .conteudo-aba {
            display: none;
            background-color: #00142a;
            border: 1px solid #002b5c;
            border-radius: 12px;
            padding: 30px;
            width: 100%;
            max-width: 1200px; /* Ocupa melhor a tela horizontalmente */
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
        }

        .conteudo-aba.ativa {
            display: block;
        }

        /* SUB-MENU SUPERIOR INTERNO DE DESCONTOS */
        .container-sub-modos {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            background-color: #000c19;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #002b5c;
            width: fit-content;
        }

        .btn-sub-modo {
            background: transparent;
            border: none;
            color: #a0aab5;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-sub-modo.ativo {
            background-color: #001A36;
            color: #00B7C3;
            border: 1px solid #002b5c;
        }

        .titulo-secao {
            color: #00B7C3;
            margin-top: 0;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .texto-explicativo {
            font-size: 13.5px;
            color: #a0aab5;
            margin-bottom: 20px;
        }

        /* FILTROS E CONTROLES DA LISTAGEM */
        .barra-ferramentas-lista {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .celula-busca {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }

        .celula-acoes {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }

        .input-filtro-busca {
            width: 100%;
            max-width: 400px;
            background-color: #000c19;
            border: 1px solid #002b5c;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        .input-filtro-busca:focus {
            border-color: #00B7C3;
        }

        .btn-marcar-todos {
            background-color: #001A36;
            border: 1px solid #002b5c;
            color: #00B7C3;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-marcar-todos:hover {
            background-color: #002b5c;
            color: #ffffff;
        }

        /* GRID DE SELEÇÃO CUSTOMIZADA */
        .grid-lotes-container {
            max-height: 280px;
            overflow-y: auto;
            border: 1px solid #002b5c;
            border-radius: 8px;
            background: #000c19;
            margin-bottom: 25px;
        }

        .item-lote-linha {
            display: table;
            width: 100%;
            padding: 12px 15px;
            border-bottom: 1px solid #002b5c;
            cursor: pointer;
            transition: background 0.2s;
        }

        .item-lote-linha:last-child {
            border-bottom: none;
        }

        .item-lote-linha:hover {
            background: #00142a;
        }

        .celula-checkbox {
            display: table-cell;
            width: 40px;
            vertical-align: middle;
            text-align: center;
        }

        .celula-checkbox input[type="checkbox"] {
            accent-color: #00B7C3;
            width: 17px;
            height: 17px;
            cursor: pointer;
        }

        .celula-info-texto {
            display: table-cell;
            vertical-align: middle;
            color: #ffffff;
            font-size: 13.5px;
            padding-left: 5px;
        }

        .celula-info-texto strong {
            color: #00B7C3;
        }

        .badge-promo {
            background: rgba(0, 183, 195, 0.15);
            color: #00B7C3;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 6px;
        }

        /* CORREÇÃO DOS CARDS DE RADIO - INFRAESTRUTURA EM TABELA */
        .painel-opcao {
            background: #001A36;
            padding: 22px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #002b5c;
        }

        .label-destaque {
            display: block;
            font-weight: bold;
            margin-bottom: 15px;
            color: #ffffff;
            font-size: 14px;
        }

        .wrapper-radios-tabela {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px 0;
            margin-left: -15px;
            margin-right: -15px;
        }

        .radio-card-cell {
            display: table-cell;
            background: #000c19;
            border: 1px solid #002b5c;
            padding: 16px 20px;
            border-radius: 6px;
            cursor: pointer;
            vertical-align: middle;
            width: 50%;
            transition: all 0.2s ease;
        }

        .radio-card-cell:hover {
            background: #001124;
            border-color: #004085;
        }

        .radio-conteudo-alinhado {
            display: table;
            width: 100%;
        }

        .radio-alinhado-input {
            display: table-cell;
            width: 30px;
            vertical-align: middle;
        }

        .radio-alinhado-input input[type="radio"] {
            accent-color: #00B7C3;
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
        }

        .radio-alinhado-label {
            display: table-cell;
            vertical-align: middle;
            font-weight: bold;
            font-size: 13.5px;
        }

        .input-porcentagem-container {
            display: flex;
            align-items: center;
            background-color: #000c19;
            border: 1px solid #002b5c;
            border-radius: 6px;
            padding: 4px 12px;
            width: fit-content;
        }

        .input-porcentagem-container input {
            border: none;
            background: transparent;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            width: 70px;
            outline: none;
        }

        .texto-off {
            font-size: 14px;
            font-weight: bold;
            color: #00B7C3;
            margin-left: 10px;
        }

        .btn-salvar {
            background: #00B7C3;
            color: #000c19;
            border: none;
            padding: 14px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(0, 183, 195, 0.2);
        }

        .btn-salvar:hover {
            background: #008fa2;
        }
    </style>
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
                </div>

                <div id="aba-descontos" class="conteudo-aba ativa">
                    
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
        </main>
    </div>

    <script>
        function alternarAba(idAba, botaoClicado) {
            document.querySelectorAll('.conteudo-aba').forEach(c => c.classList.remove('ativa'));
            document.querySelectorAll('.botao-aba').forEach(b => b.classList.remove('ativa'));
            document.getElementById(idAba).classList.add('ativa');
            botaoClicado.classList.add('ativa');
        }

        function alternarSubModo(modo) {
            if(modo === 'vencimento') {
                document.getElementById('modo-vencimento').style.display = 'block';
                document.getElementById('modo-manual').style.display = 'none';
                document.getElementById('btnModoVencimento').classList.add('ativo');
                document.getElementById('btnModoManual').classList.remove('ativo');
            } else {
                document.getElementById('modo-vencimento').style.display = 'none';
                document.getElementById('modo-manual').style.display = 'block';
                document.getElementById('btnModoVencimento').classList.remove('ativo');
                document.getElementById('btnModoManual').classList.add('ativo');
            }
        }

        // Mecanismo de busca/filtro cliente-side rápido
        function filtrarLotes(inputId, containerId) {
            let input = document.getElementById(inputId).value.toLowerCase();
            let container = document.getElementById(containerId);
            let linhas = container.getElementsByClassName('item-lote-linha');

            for (let i = 0; i < linhas.length; i++) {
                let texto = linhas[i].textContent || linhas[i].innerText;
                if (texto.toLowerCase().indexOf(input) > -1) {
                    linhas[i].style.display = "table";
                } else {
                    linhas[i].style.display = "none";
                }
            }
        }

        // Função Alternadora para a opção "Aplicar a todos / Selecionar todos"
        function marcarTodosLotes(containerId, botao) {
            let container = document.getElementById(containerId);
            let checkboxes = container.querySelectorAll('input[type="checkbox"]');
            let todasMarcadas = true;

            // Verifica o estado atual de exibição (ignora filtrados na busca)
            checkboxes.forEach(cb => {
                if(cb.closest('.item-lote-linha').style.display !== 'none' && !cb.checked) {
                    todasMarcadas = false;
                }
            });

            checkboxes.forEach(cb => {
                if(cb.closest('.item-lote-linha').style.display !== 'none') {
                    cb.checked = !todasMarcadas;
                }
            });

            botao.innerHTML = todasMarcadas ? "☑️ Selecionar Todos" : "⬜ Desmarcar Todos";
        }

        document.getElementsByName('aplicar_desconto').forEach(r => {
            r.addEventListener('change', function() {
                document.getElementById('campoPorcentagemVenc').style.display = (this.value === 'nao') ? 'none' : 'block';
            });
        });

        document.getElementsByName('aplicar_manual').forEach(r => {
            r.addEventListener('change', function() {
                document.getElementById('campoPorcentagemManual').style.display = (this.value === 'nao') ? 'none' : 'block';
            });
        });

        // Validação Submissão Vencimento
        document.getElementById('formVencimento').onsubmit = function(e) {
            e.preventDefault();
            const checks = document.querySelectorAll('input[name="lotes_vencimento[]"]:checked');
            if(checks.length === 0) {
                alert('Erro: Escolha ao menos um lote crítico ou use o botão Selecionar Todos!');
                return false;
            }
            const acao = document.querySelector('input[name="aplicar_desconto"]:checked').value;
            const pct = document.getElementById('porcentagemVenc').value;

            if(acao === 'sim') {
                if(pct < 1 || pct > 30) { alert('Erro: Limite de 1% a 30%!'); return false; }
                if(confirm(`Aplicar ${pct}% nos lotes críticos selecionados?`)) this.submit();
            } else {
                if(confirm("Retornar lotes críticos marcados ao preço original?")) this.submit();
            }
        };

        // Validação Submissão Manual
        document.getElementById('formManual').onsubmit = function(e) {
            e.preventDefault();
            const checks = document.querySelectorAll('input[name="lotes_selecionados[]"]:checked');
            if(checks.length === 0) {
                alert('Erro: Escolha ao menos um lote da lista ou utilize o botão Selecionar Todos!');
                return false;
            }
            const acao = document.querySelector('input[name="aplicar_manual"]:checked').value;
            const pct = document.getElementById('porcentagemMan').value;

            if(acao === 'sim') {
                if(pct < 1 || pct > 30) { alert('Erro: Limite de 1% a 30%!'); return false; }
                if(confirm(`Aplicar ${pct}% de desconto comercial nos lotes selecionados?`)) this.submit();
            } else {
                if(confirm("Retornar os lotes selecionados ao valor padrão?")) this.submit();
            }
        };
    </script>
</body>
</html>