function animar(id, valor) {
    let el = document.getElementById(id);
    if (!el) return; 
    
    let i = 0;
    if (valor == 0) {
        el.innerText = 0;
        return;
    }

    let intervalo = setInterval(() => {
        i += Math.ceil(valor / 15); 
        if (i >= valor) {
            i = valor;
            clearInterval(intervalo);
        }
        el.innerText = i;
    }, 40);
}

window.onload = () => {
    let vProd = parseInt(document.getElementById("prod")?.innerText) || 0;
    let vLotes = parseInt(document.getElementById("lotes_cantina")?.innerText) || 0;
    let vVenc = parseInt(document.getElementById("vencidos")?.innerText) || 0;
    let vPromo = parseInt(document.getElementById("promo")?.innerText) || 0;
    let vEstoque = parseInt(document.getElementById("estoque")?.innerText) || 0;

    animar("prod", vProd);
    animar("lotes_cantina", vLotes);
    animar("vencidos", vVenc);
    animar("promo", vPromo);
    animar("estoque", vEstoque);
};