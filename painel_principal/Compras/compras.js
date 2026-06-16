function participar(id){

    fetch(
        "participar_compra.php?id=" + id
    )
    .then(response => response.text())
    .then(retorno => {

        if(retorno.trim() !== ""){
            alert(retorno);
        }

        location.reload();

    })
    .catch(error => {

        alert(
            "Erro ao participar da compra."
        );

        console.error(error);

    });

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