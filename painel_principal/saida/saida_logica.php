<?php
// Inclui as validações de sessão e a conexão oficial com o banco de dados
include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

// Define o fuso horário padrão do sistema
date_default_timezone_set('America/Sao_Paulo');

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Captura os dados enviados pelo formulário
    $idlote           = isset($_POST['idlote']) ? intval($_POST['idlote']) : 0;
    $quantidade_saida = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 0;
    $motivo           = isset($_POST['motivo']) ? $_POST['motivo'] : '';
    
    // Valida se os campos obrigatórios foram preenchidos
    if ($idlote > 0 && $quantidade_saida > 0 && !empty($motivo)) {
        
        // 1. Consulta o lote no banco para checar a quantidade atual disponível
        $stmt_check = $conn->prepare("SELECT quantidade FROM produtoslotes WHERE idlote = ?");
        $stmt_check->bind_param("i", $idlote);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $lote = $result->fetch_assoc();

        if ($lote) {
            $quantidade_atual = intval($lote['quantidade']);

            // 2. Verifica se o estoque possui a quantidade necessária para a baixa
            if ($quantidade_atual >= $quantidade_saida) {
                
                // MUDANÇA AQUI: Inicia a transação de forma compatível com PHP antigo
                $conn->query("START TRANSACTION");

                try {
                    // 3. Executa a subtração da quantidade no lote correspondente
                    $stmt_update = $conn->prepare("UPDATE produtoslotes SET quantidade = quantidade - ? WHERE idlote = ?");
                    $stmt_update->bind_param("ii", $quantidade_saida, $idlote);
                    $stmt_update->execute();

                    // Confirma todas as alterações com sucesso no banco de dados (Compatível)
                    $conn->query("COMMIT");
                    
                    echo "<script>
                            alert('Saída de estoque registrada com sucesso!'); 
                            window.location.href = 'saida.php';
                          </script>";
                    exit();

                } catch (Exception $e) {
                    // Caso dê qualquer erro interno, desfaz as alterações de forma compatível
                    $conn->query("ROLLBACK");
                    
                    echo "<script>
                            alert('Erro interno ao processar a baixa. Tente novamente.'); 
                            window.location.href = 'saida.php';
                          </script>";
                    exit();
                }

            } else {
                echo "<script>
                        alert('Erro: Quantidade solicitada ($quantidade_saida) é maior do que a disponível em estoque ($quantidade_atual)!'); 
                        window.location.href = 'saida.php';
                      </script>";
                exit();
            }
        } else {
            echo "<script>
                    alert('Erro: Lote não encontrado ou inexistente.'); 
                    window.location.href = 'saida.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Por favor, preencha todos os campos do formulário corretamente.'); 
                window.location.href = 'saida.php';
              </script>";
        exit();
    }
} else {
    header("Location: saida.php");
    exit();
}
?>