// ==================================================
// CONFIGURAÇÃO GLOBAL
// CONFIG é carregada pelo config_scripts.php
// ==================================================

function formatMoney(valor) {

    valor = Number(valor) || 0;

    return CONFIG.simbolo + " " +
        valor.toLocaleString("pt-BR", {
            minimumFractionDigits: CONFIG.casas,
            maximumFractionDigits: CONFIG.casas
        });

}

function formatNumber(valor) {

    valor = Number(valor) || 0;

    return valor.toLocaleString("pt-BR", {
        minimumFractionDigits: CONFIG.casas,
        maximumFractionDigits: CONFIG.casas
    });

}

function formatDate(data) {

    if (!data) return "";

    let partes;

    if (data.includes("-")) {
        partes = data.substring(0,10).split("-");
    } else if (data.includes("/")) {
        partes = data.split("/");
    } else {
        return data;
    }

    let ano, mes, dia;

    if (partes[0].length == 4) {

        ano = partes[0];
        mes = partes[1];
        dia = partes[2];

    } else {

        dia = partes[0];
        mes = partes[1];
        ano = partes[2];

    }

    switch (CONFIG.formatoData) {

        case "d/m/Y":
            return `${dia}/${mes}/${ano}`;

        case "m/d/Y":
            return `${mes}/${dia}/${ano}`;

        case "Y-m-d":
            return `${ano}-${mes}-${dia}`;

        default:
            return `${dia}/${mes}/${ano}`;

    }

}

function parseMoney(valor) {

    if (!valor) return 0;

    return parseFloat(
        String(valor)
            .replace(CONFIG.simbolo, "")
            .replace(/\./g, "")
            .replace(",", ".")
            .trim()
    ) || 0;

}
function atualizarMoedas() {

    document.querySelectorAll(".money").forEach(el => {

        const valor = el.dataset.value;

        if(valor !== undefined){

            el.textContent = formatMoney(valor);

        }

    });

}

function atualizarDatas() {

    document.querySelectorAll(".date").forEach(el => {

        const data = el.dataset.value;

        if(data){

            el.textContent = formatDate(data);

        }

    });

}