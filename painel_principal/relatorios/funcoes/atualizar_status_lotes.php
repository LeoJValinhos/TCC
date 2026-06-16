<?php
// Define o fuso horário correto do Brasil para evitar o erro de timezone no servidor
date_default_timezone_set('America/Sao_Paulo');

// Captura a data atual do sistema (Ano-Mês-Dia)
$hoje = date('Y-m-d');

// 1. Atualiza para 'vencido' se a validade passou e o lote não estava com desconto/vencido antes
$sql_vencidos = "UPDATE produtoslotes 
                 SET status_lote = 'vencido' 
                 WHERE idEmpresa = $idEmpresa 
                 AND validade < '$hoje' 
                 AND status_lote NOT IN ('vencido')";

if ($conn->query($sql_vencidos) === TRUE) {
    // Log interno opcional ou apenas segue o fluxo silenciosamente para não quebrar o layout
}

// 2. Atualiza para 'promocao' se a validade está próxima (ex: nos próximos 15 dias) 
// e o lote ainda consta como 'comum'
$data_limite = date('Y-m-d', strtotime('+15 days'));
$sql_promocao = "UPDATE produtoslotes 
                 SET status_lote = 'promocao' 
                 WHERE idEmpresa = $idEmpresa 
                 AND validade >= '$hoje' 
                 AND validade <= '$data_limite' 
                 AND status_lote = 'comum'";

$conn->query($sql_promocao);
?>