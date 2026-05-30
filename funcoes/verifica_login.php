<?php

session_start();

if(
    !isset($_SESSION['nome']) ||
    !isset($_SESSION['idCadastro'])
){

    header("Location: ../registro_login/login.html");
    exit();

}

?>