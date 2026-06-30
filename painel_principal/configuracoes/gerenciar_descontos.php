<?php
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION)) {
    session_start();
}

include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;
$criadopor_nome = $_SESSION['nome'] ?? 'Sistema';
if (!$idEmpresa) {
    die("Erro Crítico: Sessão inválida.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $tipo_acao = isset($_POST['tipo_acao']) ? $_POST['tipo_acao'] : '';

    /* -------------------------------------------------------------------------
       PROCESSAMENTO: LOTES CRÍTICOS (PRÓXIMOS AO VENCIMENTO)
       ------------------------------------------------------------------------- */
    if ($tipo_acao === 'vencimento_automatico') {
        $lotes_venc = isset($_POST['lotes_vencimento']) ? $_POST['lotes_vencimento'] : [];
        $aplicar = isset($_POST['aplicar_desconto']) ? $_POST['aplicar_desconto'] : 'nao';
        $porcentagem = ($aplicar === 'sim') ? intval($_POST['porcentagem_desconto']) : 0;

        if (empty($lotes_venc)) {
            echo "<script>alert('Nenhum lote próximo ao vencimento foi selecionado!'); window.location.href='painel_principal_config.php';</script>";
            exit;
        }

        if ($porcentagem > 100) {
            echo "<script>alert('Erro: Desconto não pode ser maior que 100%!'); window.location.href='painel_principal_config.php';</script>";
            exit;
        }

        $ids_venc_formatados = implode(',', array_map('intval', $lotes_venc));

        if ($aplicar === 'sim') {
    $sql = "UPDATE produtoslotes 
            SET desconto = $porcentagem, status_lote = 'promocao',
                criadopor_nome = '$criadopor_nome'
            WHERE idEmpresa = $idEmpresa 
            AND idLote IN ($ids_venc_formatados)";
            $msg = "Sucesso: Desconto de vencimento aplicado nos itens selecionados!";
        } else {
            // Ajustado para 'normal' de acordo com a omissão/enum da imagem image_720bc8.png
            $sql = "UPDATE produtoslotes SET desconto = 0, status_lote = 'normal' 
                    WHERE idEmpresa = $idEmpresa AND idLote IN ($ids_venc_formatados)";
            $msg = "Sucesso: Descontos removidos dos itens selecionados.";
        }
        
        $conn->query($sql);
    }

    /* -------------------------------------------------------------------------
       PROCESSAMENTO: ESCOLHA MANUAL GERAL
       ------------------------------------------------------------------------- */
    elseif ($tipo_acao === 'escolha_manual') {
        $lotes = isset($_POST['lotes_selecionados']) ? $_POST['lotes_selecionados'] : [];
        $acao_manual = isset($_POST['aplicar_manual']) ? $_POST['aplicar_manual'] : 'nao';
        $porcentagem = ($acao_manual === 'sim') ? intval($_POST['porcentagem_manual']) : 0;

        if (empty($lotes)) {
            echo "<script>alert('Nenhum lote foi selecionado!'); window.location.href='painel_principal_config.php';</script>";
            exit;
        }

        if ($porcentagem > 100) {
            echo "<script>alert('Erro: Desconto não pode ser maior que 100%!'); window.location.href='painel_principal_config.php';</script>";
            exit;
        }

        $ids_formatados = implode(',', array_map('intval', $lotes));

        if ($acao_manual === 'sim') {
    $sql = "UPDATE produtoslotes 
            SET desconto = $porcentagem,
                status_lote = 'promocao',
                criadopor_nome = '$criadopor_nome'
            WHERE idEmpresa = $idEmpresa 
            AND idLote IN ($ids_formatados)";
        } else {
            // Ajustado para 'normal'
            $sql = "UPDATE produtoslotes SET desconto = 0, status_lote = 'normal' 
                    WHERE idEmpresa = $idEmpresa AND idLote IN ($ids_formatados)";
            $msg = "Sucesso: Ajustes removidos e preço original restaurado.";
        }

        $conn->query($sql);
    }

    echo "<script>alert('$msg'); window.location.href='painel_principal_config.php';</script>";
} else {
    header("Location: painel_principal_config.php");
    exit;
}