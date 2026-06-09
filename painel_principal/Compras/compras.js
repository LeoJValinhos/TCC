async function carregarProdutos(){

    const resposta =
    await fetch('api_produtos.php');

    const produtos =
    await resposta.json();

    const lista =
    document.getElementById(
    'listaProdutos'
    );

    lista.innerHTML = '';

    produtos.forEach(produto => {

        let desconto =
        (produto.price * 0.85)
        .toFixed(2);

        let participantes =
        Math.floor(
        Math.random() * 10
        ) + 1;

        let minimo = 10;

        let porcentagem =
        (participantes/minimo)*100;

        lista.innerHTML += `
        
        <div class="card">

            <img src="${produto.image}">

            <div class="info">

                <h3>
                    ${produto.title}
                </h3>

                <p class="preco-antigo">
                    R$ ${produto.price}
                </p>

                <p class="preco-coletivo">
                    R$ ${desconto}
                </p>

                <p>
                    ${participantes}/${minimo}
                    participantes
                </p>

                <div class="barra">
                    <div
                    class="progresso"
                    style="
                    width:${porcentagem}%">
                    </div>
                </div>

                <button
                class="botao">
                    Participar
                </button>

            </div>

        </div>

        `;

    });

}

carregarProdutos();

document
.getElementById('busca')
.addEventListener(
'keyup',
function(){

    let busca =
    this.value.toLowerCase();

    let cards =
    document
    .querySelectorAll('.card');

    cards.forEach(card=>{

        let titulo =
        card.querySelector('h3')
        .innerText
        .toLowerCase();

        card.style.display =
        titulo.includes(busca)
        ? 'block'
        : 'none';

    });

});