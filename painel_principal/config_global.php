<?php
include_once __DIR__ . '/../funcoes/formata.php';
if (!isset($_SESSION)) session_start();
if (!isset($conn)) include_once __DIR__ . '/../funcoes/conexao.php';

$idEmpresa = $_SESSION['idEmpresa'] ?? null;

$config = [
    'cor_primaria' => '#1cd8d8',
    'casas_decimais' => 2,
    'simbolo_moeda' => 'R$',
    'formato_data' => 'd/m/Y',
    'alerta_email' => 0,
    'alerta_login' => 0,
    'som_alerta' => 0
];

if ($idEmpresa) {
    $sql = "SELECT * FROM configuracoes_gerais WHERE idEmpresa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idEmpresa);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $config = array_merge($config, $row);
    }
}

?>