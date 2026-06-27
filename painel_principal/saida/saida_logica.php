<?php
include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $idlote           = isset($_POST['idlote']) ? intval($_POST['idlote']) : 0;
    $quantidade_saida = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
    $motivo           = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
    
    // Recupera os dados corretos conforme o seu verifica_login.php
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
                
                $conn->query("START TRANSACTION");

                try {
                    // 1. Deduz do estoque
                    $stmt_update = $conn->prepare("UPDATE produtoslotes SET quantidade = quantidade - ? WHERE idlote = ?");
                    $stmt_update->bind_param("ii", $quantidade_saida, $idlote);
                    $stmt_update->execute();

                    // 2. Grava no histórico inserindo o ID e o Nome do usuário logado
                    $stmt_history = $conn->prepare("INSERT INTO saida (idlote, id_lote, criadopor_id, criadopor_nome, quantidade_saida, motivo_saida, data_saida) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    
                    // "iiisis" mapeia os tipos: int, int, int, string, int, string
                    $stmt_history->bind_param("iiisis", $idlote, $idlote, $criadopor_id, $criadopor_nome, $quantidade_saida, $motivo);
                    $stmt_history->execute();

                    $conn->query("COMMIT");
                    
                    echo "<script>
                            alert('Saída de estoque registrada com sucesso!'); 
                            window.location.href = 'saida.php';
                          </script>";
                    exit();

                } catch (Exception $e) {
                    $conn->query("ROLLBACK");
                    echo "<script>
                            alert('Erro interno ao processar a baixa.'); 
                            window.location.href = 'saida.php';
                          </script>";
                    exit();
                }

            } else {
                echo "<script>
                        alert('Erro: Quantidade indisponível.'); 
                        window.location.href = 'saida.php';
                      </script>";
                exit();
            }
        } else {
            echo "<script>
                    alert('Erro: Lote não encontrado.'); 
                    window.location.href = 'saida.php';
                  </script>";
            exit();
        }
    }
} else {
    header("Location: saida.php");
    exit();
}
?>