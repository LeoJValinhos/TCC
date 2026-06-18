/* =====================================================
INICIALIZAÇÃO DA PÁGINA
===================================================== */

window.onload = function () {

    /* =====================================================
    CONTROLE DE LISTAS VIA URL (?lista=produtos/lotes)
    ===================================================== */

    const params = new URLSearchParams(window.location.search);
    const lista = params.get("lista");

    // Abre automaticamente a lista de produtos
    if (lista == "produtos") {

        const elProdutos = document.getElementById("listaProdutos");

        if (elProdutos) {
            elProdutos.style.display = "block";
        }
    }

    // Abre automaticamente a lista de lotes
    if (lista == "lotes") {

        const elLotes = document.getElementById("listaLotes");

        if (elLotes) {
            elLotes.style.display = "block";
        }
    }

    /* =====================================================
    ANIMAÇÕES DE CONTADORES (DASHBOARD)
    ===================================================== */

    animar("prod", 120);
    animar("ent", 80);
    animar("sai", 45);
};

/* =====================================================
FUNÇÃO DE ANIMAÇÃO DE NÚMEROS
===================================================== */

function animar(id, valor) {

    let el = document.getElementById(id);

    // proteção caso o elemento não exista
    if (!el) return;

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

/* =====================================================
MANTÉM A POSIÇÃO DA PÁGINA
===================================================== */

window.addEventListener("beforeunload", function () {
    sessionStorage.setItem("scrollY", window.scrollY);
});

window.addEventListener("load", function () {
    const scroll = sessionStorage.getItem("scrollY");

    if (scroll !== null) {
        window.scrollTo(0, parseInt(scroll));
        sessionStorage.removeItem("scrollY");
    }
});