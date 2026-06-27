<?php
require_once("../../funcoes/conexao.php");
require_once("../../funcoes/verifica_login.php");

if(!isset($_GET['id']) || !isset($_GET['acao'])){
    exit("Parâmetros inválidos");
}

$idItem = intval($_GET['id']);
$acao = $_GET['acao']; // 'participar' ou 'cancelar'
$idCadastro = $_SESSION['idCadastro'];

// Verifica se a participação já existe
$sql = "SELECT * FROM participantes_loja WHERE idItem = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idItem);
$stmt->execute();
$resPart = $stmt->get_result();
$participacao = $resPart->fetch_assoc();

if($acao === 'participar'){
    
    if(!$participacao){
        // Primeiro a participar
        $sql = "INSERT INTO participantes_loja (idItem, id_primeiroParticipante) VALUES (?, ?)";
        $s = $conn->prepare($sql); $s->bind_param("ii", $idItem, $idCadastro); $s->execute();
        
        $sql = "UPDATE loja_virtual SET quantidadeParticipantes = 1, status = 'Aguardando outro participante' WHERE idItem = ?";
        $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
    } else {
        // Já está participando?
        if($participacao['id_primeiroParticipante'] == $idCadastro || $participacao['id_segundoParticipante'] == $idCadastro) exit("Você já está participando.");
        
        // Segundo a participar
        if(empty($participacao['id_segundoParticipante'])){
            $sql = "UPDATE participantes_loja SET id_segundoParticipante = ? WHERE idItem = ?";
            $s = $conn->prepare($sql); $s->bind_param("ii", $idCadastro, $idItem); $s->execute();
            
            $sql = "UPDATE loja_virtual SET quantidadeParticipantes = 2, status = 'Concluida' WHERE idItem = ?";
            $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
        }
    }
    
} elseif($acao === 'cancelar') {
    
    if($participacao){
        if($participacao['id_primeiroParticipante'] == $idCadastro){
            // Se ele era o primeiro e tinha um segundo participante, o segundo vira o primeiro
            if(!empty($participacao['id_segundoParticipante'])){
                $sql = "UPDATE participantes_loja SET id_primeiroParticipante = id_segundoParticipante, id_segundoParticipante = NULL WHERE idItem = ?";
                $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
                
                $sql = "UPDATE loja_virtual SET quantidadeParticipantes = 1, status = 'Aguardando outro participante' WHERE idItem = ?";
                $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
            } else {
                // Se só tinha ele
                $sql = "DELETE FROM participantes_loja WHERE idItem = ?";
                $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
                
                $sql = "UPDATE loja_virtual SET quantidadeParticipantes = 0, status = 'Aberta' WHERE idItem = ?";
                $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
            }
        } elseif($participacao['id_segundoParticipante'] == $idCadastro){
            // Se ele era o segundo, removemos ele e volta a aguardar
            $sql = "UPDATE participantes_loja SET id_segundoParticipante = NULL WHERE idItem = ?";
            $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
            
            $sql = "UPDATE loja_virtual SET quantidadeParticipantes = 1, status = 'Aguardando outro participante' WHERE idItem = ?";
            $s = $conn->prepare($sql); $s->bind_param("i", $idItem); $s->execute();
        }
    }
}
echo "Sucesso";
?>