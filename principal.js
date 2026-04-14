// MENU
function toggleMenu() {
    document.getElementById("sidebar").classList.toggle("active");
}

// TEMA
function toggleTheme() {
    document.body.classList.toggle("light");
}

// ANIMAÇÃO
function contador(id, valor) {
    let el = document.getElementById(id);
    let i = 0;

    let inter = setInterval(() => {
        i += Math.ceil(valor / 40);
        if (i >= valor) {
            i = valor;
            clearInterval(inter);
        }
        el.innerText = i;
    }, 30);
}

// GRÁFICO SIMPLES
function grafico() {
    let canvas = document.getElementById("grafico");
    let ctx = canvas.getContext("2d");

    canvas.width = 500;
    canvas.height = 200;

    let dados = [50, 80, 40, 90, 70];

    ctx.beginPath();
    ctx.moveTo(0, 200 - dados[0]);

    dados.forEach((d, i) => {
        ctx.lineTo(i * 100, 200 - d);
    });

    ctx.strokeStyle = "#7c3aed";
    ctx.lineWidth = 3;
    ctx.stroke();
}

// INIT
window.onload = () => {
    contador("prod", 120);
    contador("ent", 80);
    contador("sai", 45);

    grafico();
};