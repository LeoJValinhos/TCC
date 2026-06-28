<?php
if (!isset($_SESSION)) {
    session_start();
}

include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;
if (!$idEmpresa) { 
    die("Erro: Sessão inválida."); 
}

// =========================================================================
// BLINDAGEM DA COLUNA: Verifica e cria a coluna de backup sem quebrar o MySQL
// =========================================================================
$coluna_existe = false;
$checar_coluna = $conn->query("SHOW COLUMNS FROM produtos LIKE 'estoque_minimo_original'");
if ($checar_coluna && $checar_coluna->num_rows > 0) {
    $coluna_existe = true;
}

if (!$coluna_existe) {
    // Cria a coluna sem usar o "IF NOT EXISTS" que trava o UsbWebServer
    $conn->query("ALTER TABLE produtos ADD COLUMN estoque_minimo_original INT DEFAULT NULL");
}

// =========================================================================
// AÇÃO 1: APLICAR ESTOQUE MÍNIMO PADRÃO (CORRIGIDO)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'estoque_global') {
    $qtd_global = isset($_GET['quantidade']) ? intval($_GET['quantidade']) : 10;
    if ($qtd_global < 0) { $qtd_global = 0; }

    // 1. Força a criação do backup salvando o valor ATUAL apenas para os produtos que NUNCA foram salvos antes
    $sql_backup = "UPDATE produtos SET estoque_minimo_original = estoque_minimo WHERE idEmpresa = ? AND (estoque_minimo_original IS NULL OR estoque_minimo_original = 0)";
    $stmt_b = $conn->prepare($sql_backup);
    $stmt_b->bind_param("i", $idEmpresa);
    $stmt_b->execute();
    $stmt_b->close();

    // 2. Aplica a nova quantidade padrão global para TODOS os produtos da empresa sem restrição
    $sql_update = "UPDATE produtos SET estoque_minimo = ? WHERE idEmpresa = ?";
    $stmt_u = $conn->prepare($sql_update);
    $stmt_u->bind_param("ii", $qtd_global, $idEmpresa);
    
    if ($stmt_u->execute()) {
        echo "<script>alert('Estoque mínimo modificado para $qtd_global unidades com sucesso!'); window.location.href='painel_principal_config.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar o estoque mínimo no banco.'); window.location.href='painel_principal_config.php';</script>";
    }
    $stmt_u->close();
    exit;
}

// =========================================================================
// AÇÃO 2: DESFAZER / RESTAURAR VALORES ORIGINAIS
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] === 'restaurar_original') {

    // Restaura o valor guardado no backup usando prepared statement
    $sql_restore = "UPDATE produtos SET estoque_minimo = estoque_minimo_original, estoque_minimo_original = NULL WHERE idEmpresa = ? AND estoque_minimo_original IS NOT NULL";
    $stmt_r = $conn->prepare($sql_restore);
    $stmt_r->bind_param("i", $idEmpresa);
    
    if ($stmt_r->execute()) {
        // Verifica se alguma linha foi de fato alterada
        if ($conn->affected_rows > 0) {
            echo "<script>alert('Sucesso! Os valores de estoque mínimo originais foram restaurados.'); window.location.href='painel_principal_config.php';</script>";
        } else {
            echo "<script>alert('Aviso: Nenhum valor original modificado recentemente para ser restaurado.'); window.location.href='painel_principal_config.php';</script>";
        }
    } else {
        echo "<script>alert('Erro ao processar a restauração no banco de dados.'); window.location.href='painel_principal_config.php';</script>";
    }
    $stmt_r->close();
    exit;
}

// =========================================================================
// AÇÃO 3: SALVAR DIAS DE ANTECEDÊNCIA (FORMULÁRIO POST)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dias = isset($_POST['dias_vencimento']) ? intval($_POST['dias_vencimento']) : 30;
    if ($dias < 1) { $dias = 30; }

    $sql_tabela = "CREATE TABLE IF NOT EXISTS configuracoes_alertas (
        id_config INT AUTO_INCREMENT PRIMARY KEY,
        idEmpresa INT NOT NULL,
        dias_antecedencia_vencimento INT DEFAULT 30,
        UNIQUE(idEmpresa)
    );";
    $conn->query($sql_tabela);

    $stmt = $conn->prepare("INSERT INTO configuracoes_alertas (idEmpresa, dias_antecedencia_vencimento) 
                            VALUES (?, ?) 
                            ON DUPLICATE KEY UPDATE dias_antecedencia_vencimento = ?");
    $stmt->bind_param("iii", $idEmpresa, $dias, $dias);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Configurações salvas com sucesso!'); window.location.href='painel_principal_config.php';</script>";
    exit;
}
?>