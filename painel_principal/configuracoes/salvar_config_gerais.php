<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION)) {
    session_start();
}

include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

$idEmpresa = $_SESSION['idEmpresa'];

$alerta_email = isset($_POST['alerta_email']) ? 1 : 0;
$alerta_login = isset($_POST['alerta_login']) ? 1 : 0;
$som_alerta = isset($_POST['som_alerta']) ? 1 : 0;

$casas_decimais = $_POST['casas_decimais'];
$simbolo_moeda = $_POST['simbolo_moeda'];
$formato_data = $_POST['formato_data'];

$cor_primaria = $_POST['cor_primaria'];

$sql = "
UPDATE configuracoes_gerais
SET
    alerta_email = ?,
    alerta_login = ?,
    som_alerta = ?,
    casas_decimais = ?,
    simbolo_moeda = ?,
    formato_data = ?,
    atualizado_em = NOW()
WHERE idEmpresa = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iiiissi",
    $alerta_email,
    $alerta_login,
    $som_alerta,
    $casas_decimais,
    $simbolo_moeda,
    $formato_data,
    $idEmpresa
);

$stmt->execute();

header("Location: painel_principal_config.php?salvo=1");
exit;
?>