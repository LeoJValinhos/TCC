<?php
require_once("../../funcoes/conexao.php");
require_once("../../funcoes/verifica_login.php");

header('Content-Type: application/json; charset=utf-8');

if(!isset($_GET['id'])){
    echo json_encode(['erro' => 'ID não informado']);
    exit();
}

$idItem = intval($_GET['id']);
$idUsuario = $_SESSION['idCadastro'];

// 1. Busca os dados do produto
$sqlProd = "SELECT * FROM loja_virtual WHERE idItem = ?";
$stmt = $conn->prepare($sqlProd);
$stmt->bind_param("i", $idItem);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if(!$produto) {
    echo json_encode(['erro' => 'Produto não encontrado']);
    exit();
}

// 2. Busca quem está participando
$sqlPart = "SELECT id_primeiroParticipante, id_segundoParticipante FROM participantes_loja WHERE idItem = ?";
$stmt2 = $conn->prepare($sqlPart);
$stmt2->bind_param("i", $idItem);
$stmt2->execute();
$resPart = $stmt2->get_result();

$participantesLista = [];
$isParticipando = false;

if($resPart->num_rows > 0){
    $participacao = $resPart->fetch_assoc();
    
    // Função auxiliar para buscar Nome + Empresa
    function buscarDadosUser($conn, $idUser, &$isParticipando, $idUsuarioAtual){
        if($idUser == $idUsuarioAtual) $isParticipando = true;
        
        $sql = "SELECT c.nome, c.sobrenome, e.nomeEmpresa 
                FROM cadastros c 
                LEFT JOIN empresa e ON c.idEmpresa = e.idEmpresa 
                WHERE c.idCadastro = ?";
        $st = $conn->prepare($sql);
        $st->bind_param("i", $idUser);
        $st->execute();
        $user = $st->get_result()->fetch_assoc();
        
        if($user){
            $empresa = $user['nomeEmpresa'] ? $user['nomeEmpresa'] : 'Sem empresa';
            return $user['nome'] . " " . $user['sobrenome'] . " - <i>(" . $empresa . ")</i>";
        }
        return null;
    }

    if(!empty($participacao['id_primeiroParticipante'])){
        $p1 = buscarDadosUser($conn, $participacao['id_primeiroParticipante'], $isParticipando, $idUsuario);
        if($p1) $participantesLista[] = $p1;
    }
    
    if(!empty($participacao['id_segundoParticipante'])){
        $p2 = buscarDadosUser($conn, $participacao['id_segundoParticipante'], $isParticipando, $idUsuario);
        if($p2) $participantesLista[] = $p2;
    }
}

echo json_encode([
    'produto' => $produto,
    'participantes' => $participantesLista,
    'isParticipando' => $isParticipando
]);
?>