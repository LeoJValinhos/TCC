// MENU MOBILE
function toggleMenu() {
    document.getElementById("sidebar").classList.toggle("active");
}

// DARK / LIGHT MODE
function toggleTheme() {
    document.body.classList.toggle("light");
}

// ANIMAÇÃO DE NÚMEROS
function animarNumero(id, final) {
    let el = document.getElementById(id);
    let atual = 0;

    let intervalo = setInterval(() => {
        atual += Math.ceil(final / 30);
        if (atual >= final) {
            atual = final;
            clearInterval(intervalo);
        }
        el.innerText = atual;
    }, 30);
}

// SIMULAÇÃO DE DADOS
window.onload = () => {
    animarNumero("produtos", 120);
    animarNumero("entradas", 80);
    animarNumero("saidas", 45);
};