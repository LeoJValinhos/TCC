<?php

require_once("../../funcoes/conexao.php");
require_once("../../funcoes/verifica_login.php");

if(!isset($_GET['id'])){
    exit();
}

$idItem = intval($_GET['id']);
$idCadastro = $_SESSION['idCadastro'];

/*
    Verifica se o item existe
*/

$sql = "
SELECT
    idItem,
    meta,
    quantidadeParticipantes,
    status
FROM loja_virtual
WHERE idItem = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idItem);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows == 0){
    exit();
}

$item = $resultado->fetch_assoc();

/*
    Verifica se já existe registro
*/

$sql = "
SELECT *
FROM participantes_loja
WHERE idItem = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idItem);
$stmt->execute();

$resultado = $stmt->get_result();

/*
    PRIMEIRO PARTICIPANTE
*/

if($resultado->num_rows == 0){

    $sql = "
    INSERT INTO participantes_loja
    (
        idItem,
        id_primeiroParticipante
    )
    VALUES
    (
        ?,
        ?
    )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ii",
        $idItem,
        $idCadastro
    );

    $stmt->execute();

    $sql = "
    UPDATE loja_virtual
    SET
        quantidadeParticipantes = 1,
        status = 'Aguardando outro participante'
    WHERE idItem = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idItem);
    $stmt->execute();

    exit();
}

$participacao = $resultado->fetch_assoc();

/*
    Impede participar duas vezes
*/

if(
    $participacao['id_primeiroParticipante'] == $idCadastro
    ||
    $participacao['id_segundoParticipante'] == $idCadastro
){
    exit();
}

/*
    SEGUNDO PARTICIPANTE
*/

if(
    empty($participacao['id_segundoParticipante'])
){

    $sql = "
    UPDATE participantes_loja
    SET id_segundoParticipante = ?
    WHERE idItem = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ii",
        $idCadastro,
        $idItem
    );

    $stmt->execute();

    $sql = "
    UPDATE loja_virtual
    SET
        quantidadeParticipantes = 2,
        status = 'Concluida'
    WHERE idItem = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idItem);
    $stmt->execute();
}

?>