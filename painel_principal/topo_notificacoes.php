
<div class="sininho-container-invex" style="
    position: fixed !important; 
    top: 15px !important; 
    right: 20px !important; 
    width: auto !important; 
    height: auto !important; 
    margin: 0 !important; 
    padding: 0 !important; 
    z-index: 999999 !important; 
    float: none !important; 
    display: block !important;
    clear: both !important;
">
    
    <button class="sininho-btn-invex" id="btnSininho" onclick="toggleDropdownNotificacao(event)" style="
        background: #111c2a !important; 
        border: 1px solid #1e2f45 !important; 
        padding: 8px 10px !important; 
        border-radius: 10px !important; 
        cursor: pointer !important; 
        display: flex !important; 
        align-items: center !important; 
        justify-content: center !important; 
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important; 
        position: relative !important;
        width: 42px !important;
        height: 38px !important;
        margin: 0 !important;
    ">
        <span class="sininho-icone-invex" style="font-size: 18px !important; line-height: 1 !important;">🔔</span>
        <span class="sininho-badge-invex" id="contadorSininho" style="
            display: none; 
            position: absolute !important; 
            top: -5px !important; 
            right: -5px !important; 
            background: linear-gradient(135deg, #ff334b, #ff5266) !important; 
            color: #ffffff !important; 
            font-size: 10px !important; 
            font-weight: bold !important; 
            font-family: system-ui, -apple-system, sans-serif !important; 
            border-radius: 50% !important; 
            padding: 2px 6px !important; 
            min-width: 12px !important; 
            text-align: center !important; 
            box-shadow: 0 2px 6px rgba(255, 51, 75, 0.4) !important;
            z-index: 1000000 !important;
            line-height: 1.2 !important;
        ">0</span>
    </button>

    <div class="sininho-dropdown-invex" id="dropdownSininho" style="
        display: none; 
        position: absolute !important; 
        right: 0 !important; 
        top: 45px !important; 
        background: #0d1622 !important; 
        border: 1px solid #1e2f45 !important; 
        width: 300px !important; 
        border-radius: 12px !important; 
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6) !important; 
        overflow: hidden !important; 
        z-index: 999999 !important; 
        font-family: system-ui, -apple-system, sans-serif !important;
        margin: 0 !important;
    ">
      <div class="sininho-header-invex" style="background: #111c2a !important; padding: 12px 16px !important; font-weight: 700 !important; font-size: 12px !important; color: #ffffff !important; border-bottom: 1px solid #1e2f45 !important; text-transform: uppercase !important; letter-spacing: 0.8px !important;">
            <span>Alertas Recentes</span>
        </div>
        
        <div class="sininho-body-invex" id="listaAlertasDinamica" style="max-height: 240px !important; overflow-y: auto !important;">
            <div class="sininho-vazio-invex" style="padding: 30px 16px !important; text-align: center !important; color: #647b95 !important;">
                <p style="font-size: 20px; margin: 0 0 5px 0;">🎉</p>
                <p style="margin: 0;">Carregando alertas...</p>
            </div>
        </div>
        
        <a href="<?= file_exists('painel_principal.php') ? 'configuracoes/painel_principal_config.php' : '../configuracoes/painel_principal_config.php' ?>" class="sininho-footer-invex" style="display: block !important; text-align: center !important; padding: 10px !important; font-size: 12px !important; color: #00e5ff !important; text-decoration: none !important; font-weight: 700 !important; background: #111c2a !important; border-top: 1px solid #1e2f45 !important;">
            Central de alertas ⚙️
        </a>
    </div>
</div>

<script>
    function toggleDropdownNotificacao(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('dropdownSininho');
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.setProperty('display', 'block', 'important');
        } else {
            dropdown.style.setProperty('display', 'none', 'important');
        }
    }

    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('dropdownSininho');
        const btn = document.getElementById('btnSininho');
        if (dropdown && !dropdown.contains(e.target) && !btn.contains(e.target)) {
            dropdown.style.setProperty('display', 'none', 'important');
        }
    });

    function atualizarAlertasInvex() {
        let path = window.location.pathname;
        let urlBusca = '';
        
        // Correção definitiva de rotas aceitando variações de maiúsculas/minúsculas
        if (
            path.includes('/configuracoes/') || 
            path.includes('/cadastro_produtos/') || 
            path.includes('/Compras/') || 
            path.includes('/compras/') || 
            path.includes('/consulta/') || 
            path.includes('/relatorios/') || 
            path.includes('/saida/')
        ) {
            urlBusca = '../buscar_alertas_ajax.php';
        } else {
            urlBusca = 'buscar_alertas_ajax.php';
        }

        fetch(urlBusca)
            .then(response => {
                if (!response.ok) throw new Error();
                return response.json();
            })
            .then(data => {
                const badge = document.getElementById('contadorSininho');
                const lista = document.getElementById('listaAlertasDinamica');

                if (!badge || !lista) return;

                if (data.total > 0) {
                    badge.innerText = data.total;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }

                if (!data.alertas || data.alertas.length === 0) {
                    lista.innerHTML = `
                        <div class="sininho-vazio-invex" style="padding: 30px 16px !important; text-align: center !important; color: #647b95 !important;">
                            <p style="font-size: 20px; margin: 0 0 5px 0;">🎉</p>
                            <p style="margin: 0;">Tudo limpo por aqui!</p>
                        </div>`;
                } else {
                    
                       let htmlItens = '';
                    data.alertas.forEach(alerta => {
    let bordaCor = '';
    let textoAlerta = '';

    if (alerta.tipo === 'vencido') {
        bordaCor = 'border-left: 4px solid #ff334b !important;';
        textoAlerta = `🚨 ${alerta.NomeProduto} está VENCIDO!`;
    } else if (alerta.tipo === 'vencendo') {
        bordaCor = 'border-left: 4px solid #ff9f43 !important;';
        textoAlerta = `⏳ ${alerta.NomeProduto} vence em breve.`;
    } else if (alerta.tipo === 'esgotado') {
        // NOVO ESTILO PARA ESGOTADO
        bordaCor = 'border-left: 4px solid #808080 !important;'; 
        textoAlerta = `🚫 <span style="color: #808080 !important; font-weight: 600;">${alerta.NomeProduto}</span>: ESGOTADO!`;
    } else if (alerta.tipo === 'estoque_baixo') {
        bordaCor = 'border-left: 4px solid #ffca28 !important;';
        textoAlerta = `📦 ${alerta.NomeProduto}: Estoque baixo (${alerta.quantidade} un).`;
    }

    htmlItens += `
        <div class="sininho-item-invex" style="display: flex !important; align-items: flex-start !important; gap: 10px !important; padding: 12px 16px !important; border-bottom: 1px solid #162436 !important; font-size: 13px !important; color: #b0c1d4 !important; ${bordaCor}">
            <span>${textoAlerta}</span>
        </div>`;
});
                    lista.innerHTML = htmlItens;
                }
            })
            .catch(error => console.log("Aguardando sincronização..."));
    }

    // Inicia e atualiza a cada 4 segundos de forma assíncrona
    atualizarAlertasInvex();
    setInterval(atualizarAlertasInvex, 4000);
</script>