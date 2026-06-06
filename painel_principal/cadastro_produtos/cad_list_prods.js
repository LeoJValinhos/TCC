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