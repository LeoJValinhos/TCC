<?php
// Configura retorno de texto simples para capturarmos no alert do JavaScript
header('Content-Type: text/plain; charset=utf-8');

// Ativa exibição de qualquer erro físico do interpretador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// 1. Conexão direta e independente com o banco (Evita erros de inclusão e variáveis vazias)
try {
    // Parâmetros padrão do USBWebServer
    $host = 'localhost';
    $db   = 'databasetcc';
    $user = 'root';
    $pass = 'usbw'; 
    
    $banco = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $banco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "ERRO DE CONEXÃO COM O BANCO: " . $e->getMessage();
    exit;
}

// 2. Coleta e validação básica dos dados enviados pelo JavaScript
$idProduto   = isset($_POST['idProduto']) ? trim($_POST['idProduto']) : '';
$tipo        = isset($_POST['tipo']) ? trim($_POST['tipo']) : '';
$numero_lote = !empty($_POST['numero_lote']) ? trim($_POST['numero_lote']) : null;

if ($idProduto === '' || $tipo === '') {
    echo "ERRO DADOS: O JavaScript não enviou idProduto ou tipo.";
    exit;
}

// 3. Gerenciamento do ID da Empresa (Verifica na sessão ou define como nulo para não travar)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;

// 4. Execução da Query com nomenclatura idêntica ao phpMyAdmin
try {
    $sql = "INSERT INTO alertas_ocultos (idProduto, numero_lote, tipo_alerta, idEmpresa, data_ocultado) 
            VALUES (:id_prod, :lote, :tipo, :id_emp, NOW())";
            
    $stmt = $banco->prepare($sql);
    
    $stmt->bindValue(':id_prod', $idProduto, PDO::PARAM_INT);
    $stmt->bindValue(':lote', $numero_lote, $numero_lote === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindValue(':id_emp', $idEmpresa, $idEmpresa === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo "OK_SUCESSO";
    } else {
        echo "ERRO GRAVAÇÃO: O banco de dados recusou os dados informados.";
    }

} catch (PDOException $e) {
    echo "MENSAGEM DO MYSQL: " . $e->getMessage();
} catch (Exception $e) {
    echo "MENSAGEM GERAL DO PHP: " . $e->getMessage();
}
exit;