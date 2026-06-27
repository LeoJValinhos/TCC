function animar(id, valor) {
    let el = document.getElementById(id);
    if (!el) return; // Garante que o elemento existe na página antes de animar

    let i = 0;
    
    // Se o valor for 0, já define como 0 e não gasta processamento com intervalo
    if (valor <= 0) {
        el.innerText = 0;
        return;
    }

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
    // 1. Pega o elemento HTML do total de produtos
    let prodElemento = document.getElementById("prod");
    
    if (prodElemento) {
        // 2. Extrai o número que o PHP renderizou dentro dele
        let valorRealProd = parseInt(prodElemento.innerText) || 0;
        
        // 3. Passa o valor real para a função de animação
        animar("prod", valorRealProd);
    }

    // Para os outros cards (se você tiver os IDs "ent" e "sai" no seu HTML), 
    // faça a mesma lógica ou deixe fixo por enquanto se ainda não tiver o PHP deles:
    animar("ent", 80);
    animar("sai", 45);
};