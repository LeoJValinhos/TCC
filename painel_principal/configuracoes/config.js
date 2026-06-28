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