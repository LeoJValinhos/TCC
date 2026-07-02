let modalIdAtual = null;
let produtoAtualModal = null; // Variável para guardar os dados do banco e usar na conta

function formatarMoeda(valor) {
    return (
        configGeral.simboloMoeda +
        " " +
        Number(valor).toLocaleString("pt-BR", {
            minimumFractionDigits: configGeral.casasDecimais,
            maximumFractionDigits: configGeral.casasDecimais
        })
    );
}

function abrirModal(idItem) {
    modalIdAtual = idItem;
    document.getElementById("modalCompra").style.display = "flex";
    carregarDadosModal();
}

function fecharModal() {
    document.getElementById("modalCompra").style.display = "none";
    modalIdAtual = null;
    produtoAtualModal = null;
    location.reload(); 
}



function carregarDadosModal() {
    fetch("detalhes_compra.php?id=" + modalIdAtual)
    .then(response => response.json())
    .then(data => {
        if(data.erro) { alert(data.erro); fecharModal(); return; }

        produtoAtualModal = data.produto; // Salva o produto para o simulador
        let prod = produtoAtualModal;

        // Preenche info originais
        document.getElementById("modalTitulo").innerText = prod.nomeProduto;
        document.getElementById("modalMarca").innerText = "Marca: " + prod.marcaProduto;
        document.getElementById("modalDescricao").innerText = prod.descricaoProduto;
        document.getElementById("modalImagem").src = prod.imagemProduto;
        document.getElementById("modalQtdPart").innerText = prod.quantidadeParticipantes;

        // PUXANDO DO BANCO (Substituindo os valores em branco)
        document.getElementById("modalFornecedor").innerText = prod.fornecedor || "Não informado";
        document.getElementById("modalPrecoTotal").innerText = formatarMoeda(prod.valor_total);
        document.getElementById("modalPrecoUnitario").innerText = formatarMoeda(prod.valor_unitario);

        // Lógica da caixinha de aviso de desconto
        let divDesconto = document.getElementById("modalDescontoInfo");
        let textoDesconto = document.getElementById("textoDesconto");
        
        let descValor = parseFloat(prod.descontopor_quantidade_produto) || 0;
        let minQtd = parseInt(prod.quantidade_deproduto_minimo_desconto) || 0;

        if(descValor > 0 && minQtd > 0) {
            divDesconto.style.display = "block";
            textoDesconto.innerHTML = `⚠️ <strong>Desconto de Atacado:</strong> Levando a partir de <strong>${minQtd} unidades</strong>, você ganha <strong>${formatarMoeda(descValor)}</strong> de desconto em CADA unidade!`;
        } else {
            divDesconto.style.display = "none";
        }

        // Configura o input de quantidade para calcular automático
        let inputQtd = document.getElementById("qtdComprar");
        inputQtd.value = 1; 
        inputQtd.oninput = calcularEstimativa; 
        calcularEstimativa(); 

        // Lista Participantes
        let ul = document.getElementById("listaParticipantes");
        ul.innerHTML = "";
        if(data.participantes.length > 0){
            data.participantes.forEach(nome => {
                let li = document.createElement("li");
                li.innerHTML = "👤 " + nome;
                ul.appendChild(li);
            });
        } else {
            ul.innerHTML = "<li>Ninguém participando ainda. Seja o primeiro!</li>";
        }

        // Configura os Botões
        let btnPart = document.getElementById("btnParticiparModal");
        let btnCanc = document.getElementById("btnCancelarModal");

        btnPart.onclick = null;
        btnCanc.onclick = null;

        if (data.isParticipando) {
            btnPart.disabled = true;
            btnPart.style.background = "#555";
            btnPart.style.color = "#aaa";

            btnCanc.disabled = false;
            btnCanc.classList.remove('btn-inativo');
            btnCanc.onclick = () => acaoCompra('cancelar');
        } else {
            btnCanc.disabled = true;
            btnCanc.classList.add('btn-inativo');

            if(prod.quantidadeParticipantes < prod.meta){
                btnPart.disabled = false;
                btnPart.style.background = "linear-gradient(90deg, #00B7C3, #00F5D4)";
                btnPart.style.color = "#02152E";
                btnPart.onclick = () => acaoCompra('participar');
            } else {
                btnPart.disabled = true;
                btnPart.innerText = "Lotado";
                btnPart.style.background = "#555";
            }
        }
    })
    .catch(error => console.error(error));
}

// === SIMULADOR MATEMÁTICO ===
function calcularEstimativa() {
    if (!produtoAtualModal) return;
    
    let qtdDigitada = parseInt(document.getElementById("qtdComprar").value) || 0;
    
    // Valores do Banco de Dados
    let precoBase = parseFloat(produtoAtualModal.valor_unitario) || 0;
    let desconto = parseFloat(produtoAtualModal.descontopor_quantidade_produto) || 0;
    let qtdMinima = parseInt(produtoAtualModal.quantidade_deproduto_minimo_desconto) || 0;
    
    let precoAplicado = precoBase;
    let spanCusto = document.getElementById("modalCustoCalculado");

    // Aplica o desconto do banco se atingir a quantidade mínima
    if (desconto > 0 && qtdMinima > 0 && qtdDigitada >= qtdMinima) {
        precoAplicado = precoBase - desconto;
        spanCusto.style.color = "#ffae42"; // Laranja para mostrar que pegou promoção
    } else {
        spanCusto.style.color = "#00F5D4"; // Ciano normal
    }

    let totalEstimado = qtdDigitada * precoAplicado;
    
    if(totalEstimado < 0) totalEstimado = 0;

    spanCusto.innerText = formatarMoeda(totalEstimado);
}

function acaoCompra(acao) {
    // Pega o valor que o usuário digitou no input
    let qtd = document.getElementById("qtdComprar").value;

    // Envia a quantidade na URL junto com o id e a acao
    fetch("participar_compra.php?id=" + modalIdAtual + "&acao=" + acao + "&qtd=" + qtd)
    .then(response => response.text())
    .then(retorno => {
        carregarDadosModal(); 
    });
}