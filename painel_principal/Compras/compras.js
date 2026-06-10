function participar(id){

    fetch(
        "participar_compra.php?id=" + id
    )
    .then(response => response.text())
    .then(() => {

        alert("Participação realizada com sucesso!");

        location.reload();

    });

}