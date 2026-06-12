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