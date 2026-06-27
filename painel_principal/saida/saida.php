<?php
include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

$idEmpresa = $_SESSION['idEmpresa'];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="saida.css">
    <link rel="icon" type="image/png" href="../Imagens/Carrinho.png" width="70" height="70">
    <title>INVEX - Saída de Produtos</title>
    <style>
        /* Ajustes inline para garantir que o layout quebre as amarras de largura máxima */
        .layout {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .main {
            flex: 1;
            padding: 30px;
            box-sizing: border-box;
            background-color: #030e1e; /* Mantendo o fundo escuro padrão */
        }

        /* Faz o container central se espalhar por toda a tela */
        .container-saida-central {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* O card agora ocupa 100% da área útil */
        .card-neon-invex {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
            background: #05162e;
            border: 1px solid #00f2ff33;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.1);
        }

        /* Input de busca ocupando toda a largura útil */
        .search-wrapper-invex {
            width: 100%;
            position: relative;
        }

        .input-search-invex {
            width: 100% !important;
            box-sizing: border-box;
        }

        /* Alarga o painel de resultados do dropdown */
        .dropdown-results-invex {
            width: 100% !important;
            max-height: 450px; /* Aumentado para ver mais linhas de uma vez */
            overflow-y: auto;
            background: #030e1e;
            border: 1px solid #00f2ff55;
            border-radius: 8px;
            margin-top: 5px;
        }

        /* Tabela esticada para preencher a tela */
        .table-results-invex {
            width: 100% !important;
            border-collapse: collapse;
        }

        .table-results-invex th, 
        .table-results-invex td {
            padding: 14px 18px !important; /* Mais espaçoso para leitura */
            text-align: left;
        }

        /* Grid para os campos de quantidade e motivo ficarem lado a lado em telas cheias */
        .grid-campos-saida {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 25px;
        }

        .grupo-campo-saida {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .grupo-campo-saida input,
        .grupo-campo-saida select {
            width: 100% !important;
            box-sizing: border-box;
            padding: 12px;
            background: #030e1e;
            border: 1px solid #475569;
            color: #fff;
            border-radius: 6px;
        }

        /* Painel de valores em largura cheia */
        .panel-valores-invex {
            width: 100%;
            display: flex;
            gap: 20px;
            margin-top: 25px;
        }

        .valor-card {
            flex: 1;
        }
    </style>
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
                <a href="saida.php">⬅️ Saida</a>
                <a href="../consulta/consulta.php">📦 Consulta</a>
                <a href="../compras/compras.php">🛒 Compras</a>
                <a href="../relatorios/buscar_relatorio.php">📊 Relatórios</a>
                <a href="../configuracoes/painel_principal_config.php">⚙️ Configurações</a>
            </nav>
            <a href="../index.html" class="logout">🚪 Sair</a>
        </aside>

        <main class="main">
            
            <div class="top">
                <h2>Saída de produtos</h2>
                <p class="subtitulo">Gerencie a baixa de itens do estoque geral da empresa.</p>
            </div>

            <div class="container-saida-central">
                <div class="card-neon-invex">
                    <h2 class="titulo-card">Registrar saída</h2>
                    
                    <form id="formSaida" action="saida_logica.php" method="POST">
                        
                        <div class="form-group-invex">
                            <label class="label-invex">Selecione o lote do produto</label>
                            
                            <div class="search-wrapper-invex">
                                <input type="text" id="inputBusca" class="input-search-invex" placeholder="Digite o nome ou marca para filtrar os lotes..." autocomplete="off">
                                <span class="icon-search-invex">🔍</span>
                            </div>
                            
                            <input type="hidden" id="idlote_selecionado" name="idlote" required>

                            <div class="dropdown-results-invex" id="dropdownLotes">
                                <table class="table-results-invex">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%;">Produto / Marca</th>
                                            <th style="width: 25%;">Lote / Validade</th>
                                            <th style="width: 20%; text-align: right;">Qtd Disponível</th>
                                            <th style="width: 15%; text-align: right;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT l.idlote, l.numero_lote, l.quantidade, l.validade, l.preco_venda, l.desconto, p.NomeProduto, p.MarcaProduto 
                                                  FROM produtoslotes l 
                                                  INNER JOIN produtos p ON l.idproduto = p.idProduto 
                                                  WHERE p.idEmpresa = $idEmpresa AND l.quantidade > 0 
                                                  ORDER BY p.NomeProduto ASC";
                                        
                                        $result = $conn->query($query);
                                        
                                        if($result && $result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                
                                                $classe_data = "text-muted";
                                                $badge_classe = "badge-stable";
                                                $badge_texto = "Estável";
                                                
                                                if (!empty($row['validade'])) {
                                                    $hoje = new DateTime();
                                                    $data_validade = new DateTime($row['validade']);
                                                    $data_formatada = $data_validade->format('d/m/Y');
                                                    
                                                    if($data_validade < $hoje) {
                                                        $classe_data = "text-critical";
                                                        $badge_classe = "badge-critical";
                                                        $badge_texto = "Vencido";
                                                    } else {
                                                        $dias = $hoje->diff($data_validade)->days;
                                                        if($dias <= 30) {
                                                            $classe_data = "text-critical";
                                                            $badge_classe = "badge-critical";
                                                            $badge_texto = "Crítico";
                                                        } elseif($dias <= 60) {
                                                            $classe_data = "text-warning";
                                                            $badge_classe = "badge-warning";
                                                            $badge_texto = "Atenção";
                                                        }
                                                    }
                                                } else {
                                                    $data_formatada = "Sem data cadastrada";
                                                }

                                                $preco = isset($row['preco_venda']) ? $row['preco_venda'] : 0.00;
                                                $desconto = isset($row['desconto']) ? $row['desconto'] : 0.00;
                                                $desconto_int = intval($desconto);

                                                echo "<tr class='item-row-invex' data-id='{$row['idlote']}' data-preco='{$preco}' data-desconto='{$desconto_int}' data-busca='".strtolower($row['NomeProduto']." ".$row['MarcaProduto'])."'>";
                                                echo "  <td>";
                                                echo "      <div class='prod-name'>".htmlspecialchars($row['NomeProduto'])."</div>";
                                                echo "      <div class='prod-brand'>".htmlspecialchars($row['MarcaProduto'])."</div>";
                                                echo "  </td>";
                                                echo "  <td>";
                                                echo "      <div class='lot-number'>Lote: ".htmlspecialchars($row['numero_lote'])."</div>";
                                                echo "      <div class='lot-date {$classe_data}'>Val: {$data_formatada}</div>";
                                                echo "  </td>";
                                                echo "  <td class='text-right'><span class='qty-highlight'>{$row['quantidade']}</span> un</td>";
                                                echo "  <td class='text-right'><span class='badge-invex {$badge_classe}'>{$badge_texto}</span></td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' style='text-align:center; color:#64748b; padding:20px;'>Nenhum lote disponível no momento.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="panel-valores-invex" id="panelValores" style="display: none;">
                            <div class="valor-card">
                                <span class="valor-label">Preço unitário</span>
                                <div class="valor-row">
                                    <span class="valor-destaque" id="txtPrecoUnitario">R$ 0,00</span>
                                    <span class="badge-desconto-invex" id="tagDesconto" style="display: none;">0% OFF</span>
                                </div>
                            </div>
                            <div class="valor-card total-box">
                                <span class="valor-label">Subtotal geral</span>
                                <span class="valor-destaque total-color" id="txtPrecoTotal">R$ 0,00</span>
                            </div>
                        </div>

                        <div class="grid-campos-saida">
                            <div class="grupo-campo-saida">
                                <label for="quantidade">Quantidade de saída</label>
                                <input type="number" id="quantidade" name="quantidade" min="1" value="1" required>
                            </div>

                            <div class="grupo-campo-saida">
                                <label for="motivo">Motivo</label>
                                <select id="motivo" name="motivo" required>
                                    <option value="Venda">Venda</option>
                                    <option value="Vencimento">Produto Vencido</option>
                                    <option value="Avaria">Avaria / Danificado</option>
                                    <option value="Ajuste">Ajuste de Inventário</option>
                                </select>
                            </div>
                        </div>

                        <div class="container-botao-saida" style="margin-top: 30px;">
                            <button type="submit" class="btn-confirmar-neon" style="width: 100%;">Confirmar saída</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputBusca = document.getElementById("inputBusca");
            const dropdownLotes = document.getElementById("dropdownLotes");
            const idLoteInput = document.getElementById("idlote_selecionado");
            const inputQuantidade = document.getElementById("quantidade");
            const linhas = document.querySelectorAll(".item-row-invex");
            
            const panelValores = document.getElementById("panelValores");
            const txtPrecoUnitario = document.getElementById("txtPrecoUnitario");
            const tagDesconto = document.getElementById("tagDesconto");
            const txtPrecoTotal = document.getElementById("txtPrecoTotal");
            const selectMotivo = document.getElementById("motivo");

            let precoAtual = 0;
            let descontoAtual = 0;

            inputBusca.addEventListener("focus", () => {
                dropdownLotes.style.display = "block";
            });

            document.addEventListener("click", function(e) {
                if (!inputBusca.contains(e.target) && !dropdownLotes.contains(e.target)) {
                    dropdownLotes.style.display = "none";
                }
            });

            inputBusca.addEventListener("input", function() {
                const termo = this.value.toLowerCase().trim();
                dropdownLotes.style.display = "block";

                linhas.forEach(linha => {
                    const textoBusca = linha.getAttribute("data-busca");
                    if (textoBusca.includes(termo)) {
                        linha.style.display = "";
                    } else {
                        linha.style.display = "none";
                    }
                });
            });

            function atualizarPainelPrecos() {
                let qtd = parseInt(inputQuantidade.value) || 0;
                
                if(selectMotivo.value !== "Venda") {
                    tagDesconto.style.display = "none";
                    txtPrecoUnitario.innerText = "R$ 0,00";
                    txtPrecoTotal.innerText = "R$ 0,00 (Baixa de Estoque)";
                    return;
                }

                if (precoAtual > 0) {
                    panelValores.style.display = "flex";
                    
                    let precoComDesconto = precoAtual;
                    if (descontoAtual > 0) {
                        precoComDesconto = precoAtual - (precoAtual * (descontoAtual / 100));
                        tagDesconto.innerText = descontoAtual + "% OFF";
                        tagDesconto.style.display = "inline-block";
                    } else {
                        tagDesconto.style.display = "none";
                    }

                    let total = precoComDesconto * qtd;

                    txtPrecoUnitario.innerText = precoAtual.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    txtPrecoTotal.innerText = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                } else {
                    panelValores.style.display = "none";
                }
            }

            inputQuantidade.addEventListener("input", atualizarPainelPrecos);
            selectMotivo.addEventListener("change", atualizarPainelPrecos);

            linhas.forEach(linha => {
                linha.addEventListener("click", function() {
                    linhas.forEach(l => l.classList.remove("selected"));
                    this.classList.add("selected");
                    
                    const idLote = this.getAttribute("data-id");
                    const nomeProd = this.querySelector(".prod-name").innerText;
                    const numeroLote = this.querySelector(".lot-number").innerText;
                    
                    precoAtual = parseFloat(this.getAttribute("data-preco")) || 0;
                    descontoAtual = parseInt(this.getAttribute("data-desconto")) || 0;

                    idLoteInput.value = idLote;
                    inputBusca.value = nomeProd + " (" + numeroLote + ")";
                    
                    dropdownLotes.style.display = "none";
                    
                    panelValores.style.display = "flex";
                    atualizarPainelPrecos();
                });
            });
        });
    </script>
</body>
</html>