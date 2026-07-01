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

            // =========================================================================
//  FUNÇÕES DA CENTRAL DE ALERTAS DE ESTOQUE
// =========================================================================

/**
 * Filtra os alertas em tempo real por texto digitado e tipo selecionado
 */
function filtrarAlertas() {
    const textoBusca = document.getElementById('buscaAlerta').value.toLowerCase();
    const tipoSelecionado = document.getElementById('filtroTipoAlerta').value;
    const cards = document.querySelectorAll('.card-alerta');

    cards.forEach(card => {
        const infoTexto = card.querySelector('.celula-info-texto').textContent.toLowerCase();
        const tipoAlerta = card.getAttribute('data-tipo');

        // Valida busca por texto
        const bateTexto = infoTexto.includes(textoBusca);
        // Valida filtro por tipo de alerta
        const bateTipo = (tipoSelecionado === 'todos' || tipoAlerta === tipoSelecionado);

        if (bateTexto && bateTipo) {
            card.style.setProperty('display', 'flex', 'important');
        } else {
            card.style.setProperty('display', 'none', 'important');
        }
    });

    verificarListaVazia();
}

/**
 * Ordena os alertas dinamicamente com base nas datas (Crescente ou Decrescente)
 */
function ordenarAlertas() {
    const container = document.getElementById('listaAlertasContainer');
    const ordenacao = document.getElementById('ordenacaoAlerta').value;
    const cards = Array.from(container.querySelectorAll('.card-alerta'));

    cards.sort((a, b) => {
        const dataA = new Date(a.getAttribute('data-data'));
        const dataB = new Date(b.getAttribute('data-data'));

        if (ordenacao === 'asc') {
            return dataA - dataB; // Mais antigos primeiro
        } else {
            return dataB - dataA; // Mais recentes primeiro
        }
    });

    // Reordena visualmente os elementos dentro do contêiner
    cards.forEach(card => container.appendChild(card));
}

/**
 * Apaga visualmente uma notificação (Alerta)
 * Dica: Em um ambiente real, você pode disparar um fetch() aqui para o PHP remover do Banco de Dados
 */
function excluirAlerta(botao, idAlerta) {
    if (confirm("Deseja mesmo remover esta notificação do painel?")) {
        const cardAlerta = botao.closest('.card-alerta');
        
        // Efeito visual sumindo simples
        cardAlerta.style.opacity = '0';
        setTimeout(() => {
            cardAlerta.remove();
            verificarListaVazia();
        }, 300);
        
        // Caso queira integrar com banco futuramente, a lógica seria:
        // fetch(`apagar_alerta.php?id=${idAlerta}`, { method: 'DELETE' });
    }
}

/**
 * Verifica se todos os alertas foram limpos ou filtrados e exibe mensagem de lista vazia
 */
function verificarListaVazia() {
    const container = document.getElementById('listaAlertasContainer');
    const cardsVisiveis = container.querySelectorAll('.card-alerta:not([style*="display: none"])');
    
    // Remove mensagem anterior se existir
    const msgAntiga = container.querySelector('.msg-vazia-alertas');
    if (msgAntiga) msgAntiga.remove();

    if (cardsVisiveis.length === 0) {
        const msg = document.createElement('p');
        msg.className = 'msg-vazia-alertas';
        msg.style.cssText = 'padding:15px; color:#a0aab5; margin:0; font-style:italic;';
        msg.textContent = 'Nenhuma notificação encontrada com os filtros atuais.';
        container.appendChild(msg);
    }
}

// Inicializa a ordenação padrão ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('listaAlertasContainer')) {
        ordenarAlertas();
    }
});

// Funções de controle do Modal de Restauração
function abrirModalRestaurar() {
    document.getElementById('customModalRestaurar').style.display = 'flex';
}

function fecharModalRestaurar() {
    document.getElementById('customModalRestaurar').style.display = 'none';
}

function confirmarRestauracao() {
    // Redireciona o fluxo para executar a restauração de backup no PHP
    window.location.href = 'processar_config_alertas.php?action=restaurar_original';
}
        };

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