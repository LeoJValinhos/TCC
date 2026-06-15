window.onload = function(){

    const params =
    new URLSearchParams(window.location.search);

    const lista = params.get("lista");

    if(lista == "produtos"){

        document.getElementById("listaProdutos")
        .style.display = "block";

    }

    if(lista == "lotes"){

        document.getElementById("listaLotes")
        .style.display = "block";

    }

}

function mostrarListaProdutos(){

    let lista =
    document.getElementById("listaProdutos");

    lista.style.display =
    lista.style.display == "none"
    ? "block"
    : "none";

}

function mostrarListaLotes(){

    let lista =
    document.getElementById("listaLotes");

    lista.style.display =
    lista.style.display == "none"
    ? "block"
    : "none";

}

function animar(id, valor) {
    let el = document.getElementById(id);
    let i = 0;

    let intervalo = setInterval(() => {
        i += Math.ceil(valor / 30);
        if (i >= valor) {
            i = valor;
            clearInterval(intervalo);
        }
        el.innerText = i;
    }, 30);
}

window.onload = () => {
    animar("prod", 120);
    animar("ent", 80);
    animar("sai", 45);
};