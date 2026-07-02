<?php
include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

// Função auxiliar para exibir o alerta SweetAlert2 sem interromper o fluxo bruscamente
function exibirAlerta($icon, $title, $text, $redirect) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5/dark.min.css'>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                background: '#1f1f1f',
                color: '#ffffff',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendi'
            }).then(() => {
                window.location.href = '$redirect';
            });
        });
    </script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $idlote          = isset($_POST['idlote']) ? intval($_POST['idlote']) : 0;
    $quantidade_saida = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
    $motivo          = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
    
    $criadopor_id   = isset($_SESSION['idCadastro']) ? intval($_SESSION['idCadastro']) : 0;
    $criadopor_nome = isset($_SESSION['nome']) ? trim($_SESSION['nome']) : '';
    
    if ($idlote > 0 && $quantidade_saida > 0 && !empty($motivo)) {
        
        $stmt_check = $conn->prepare("SELECT quantidade FROM produtoslotes WHERE idlote = ?");
        $stmt_check->bind_param("i", $idlote);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $lote = $result->fetch_assoc();

        if ($lote) {
            $quantidade_atual = intval($lote['quantidade']);

            if ($quantidade_atual >= $quantidade_saida) {
                
                $conn->begin_transaction(); // Melhor prática que $conn->query("START TRANSACTION")

                try {
                    // 1. Deduz do estoque
                    $stmt_update = $conn->prepare("UPDATE produtoslotes SET quantidade = quantidade - ? WHERE idlote = ?");
                    $stmt_update->bind_param("ii", $quantidade_saida, $idlote);
                    $stmt_update->execute();

                    // 2. Grava no histórico
                    $stmt_history = $conn->prepare("INSERT INTO saida (idlote, id_lote, criadopor_id, criadopor_nome, quantidade_saida, motivo_saida, data_saida) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    $stmt_history->bind_param("iiisis", $idlote, $idlote, $criadopor_id, $criadopor_nome, $quantidade_saida, $motivo);
                    $stmt_history->execute();

                    $conn->commit();
                    
                    exibirAlerta('success', 'Sucesso!', 'Saída de estoque registrada com sucesso!', 'saida.php');

                } catch (Exception $e) {
                    $conn->rollback();
                    exibirAlerta('error', 'Erro!', 'Erro interno ao processar a baixa.', 'saida.php');
                }

            } else {
                exibirAlerta('warning', 'Atenção!', 'Erro: Quantidade indisponível.', 'saida.php');
            }
        } else {
            exibirAlerta('error', 'Erro!', 'Erro: Lote não encontrado.', 'saida.php');
        }
    } else {
        exibirAlerta('error', 'Erro!', 'Preencha todos os campos corretamente.', 'saida.php');
    }
} else {
    header("Location: saida.php");
    exit();
}
?>