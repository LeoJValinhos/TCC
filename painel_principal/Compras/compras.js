let modalIdAtual = null;

function abrirModal(idItem) {
    modalIdAtual = idItem;
    document.getElementById("modalCompra").style.display = "flex";
    carregarDadosModal();
}

function fecharModal() {
    document.getElementById("modalCompra").style.display = "none";
    modalIdAtual = null;
    location.reload(); // Recarrega para atualizar as barrinhas no fundo
}

function carregarDadosModal() {
    fetch("detalhes_compra.php?id=" + modalIdAtual)
    .then(response => response.json())
    .then(data => {
        if(data.erro) { alert(data.erro); fecharModal(); return; }

        let prod = data.produto;

        // Preenche info
        document.getElementById("modalTitulo").innerText = prod.nomeProduto;
        document.getElementById("modalMarca").innerText = "Marca: " + prod.marcaProduto;
        document.getElementById("modalDescricao").innerText = prod.descricaoProduto;
        document.getElementById("modalImagem").src = prod.imagemProduto;
        document.getElementById("modalQtdPart").innerText = prod.quantidadeParticipantes;

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

        // Remove listeners antigos para evitar duplicação de cliques
        btnPart.onclick = null;
        btnCanc.onclick = null;

        if (data.isParticipando) {
            // Se já participa: Participar desativado, Cancelar vermelhão e ativado
            btnPart.disabled = true;
            btnPart.style.background = "#555";
            btnPart.style.color = "#aaa";

            btnCanc.disabled = false;
            btnCanc.classList.remove('btn-inativo');
            btnCanc.onclick = () => acaoCompra('cancelar');
        } else {
            // Se NÃO participa:
            btnCanc.disabled = true;
            btnCanc.classList.add('btn-inativo');

            // Se ainda tem vaga:
            if(prod.quantidadeParticipantes < prod.meta){
                btnPart.disabled = false;
                btnPart.style.background = "linear-gradient(90deg, #00B7C3, #00F5D4)";
                btnPart.style.color = "#02152E";
                btnPart.onclick = () => acaoCompra('participar');
            } else {
                // Compra lotada e ele não participa
                btnPart.disabled = true;
                btnPart.innerText = "Lotado";
                btnPart.style.background = "#555";
            }
        }
    })
    .catch(error => console.error(error));
}

function acaoCompra(acao) {
    fetch("participar_compra.php?id=" + modalIdAtual + "&acao=" + acao)
    .then(response => response.text())
    .then(retorno => {
        carregarDadosModal(); // Atualiza o modal na hora, sem piscar a tela
    });
}