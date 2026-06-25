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