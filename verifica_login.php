<?php
session_start();

if(!isset($_SESSION['nome']) || !isset($_SESSION['idCadastro'])){
    header("Location: login.html");
    exit();
}
?>