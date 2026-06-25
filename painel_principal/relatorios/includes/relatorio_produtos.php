<?php
// Garante que a sessão está ativa para não perder o idEmpresa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Captura o idEmpresa direto da sessão do usuário logado
$idEmpresa = isset($_SESSION['idEmpresa']) ? intval($_SESSION['idEmpresa']) : 0;

if ($idEmpresa > 0) {
    // Busca os produtos cadastrados fazendo INNER JOIN com os lotes para trazer os preços reais
    $sql = "SELECT p.NomeProduto, l.numero_lote, l.quantidade, l.validada, l.preco_compra, l.preco_venda, l.desconto, l.status_lote
            FROM produtoslotes l
            INNER JOIN produtos p ON l.idproduto = p.idProduto
            WHERE l.idEmpresa = $idEmpresa 
            ORDER BY p.NomeProduto ASC, l.numero_lote ASC";
            
    $result = $conn->query($sql);
}
?>

<table style="width: 100%; border-collapse: collapse; background: transparent;">
    <thead>
        <tr style="border-bottom: 2px solid #00f5d4; text-align: left;">
            <th style="padding: 15px; color: #00f5d4; font-size: 14px; font-weight: bold; text-transform: uppercase;">Produto</th>
            <th style="padding: 15px; color: #00f5d4; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center;">Nº Lote</th>
            <th style="padding: 15px; color: #00f5d4; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: right;">Preço Compra</th>
            <th style="padding: 15px; color: #00f5d4; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: right;">Preço Venda</th>
            <th style="padding: 15px; color: #00f5d4; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: center;">Desconto</th>
            <th style="padding: 15px; color: #00f5d4; font-size: 14px; font-weight: bold; text-transform: uppercase; text-align: right;">Venda Final</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($idEmpresa > 0 && $result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): 
                $lote = !empty($row['numero_lote']) ? '#'.$row['numero_lote'] : '-';
                $p_compra = floatval($row['preco_compra']);
                $p_venda_base = floatval($row['preco_venda']);
                $desc = floatval($row['desconto']);
                
                // Preço final de venda considerando a porcentagem do desconto
                $p_venda_final = $p_venda_base * (1 - ($desc / 100));
            ?>
                <tr style="border-bottom: 1px solid rgba(0, 245, 212, 0.1); font-size: 15px;">
                    <td style="padding: 20px 15px; color: #ffffff; font-weight: bold;"><?= htmlspecialchars($row['NomeProduto']) ?></td>
                    <td style="padding: 20px 15px; color: #94a3b8; text-align: center;"><?= $lote ?></td>
                    <td style="padding: 20px 15px; color: #ffffff; text-align: right;">R$ <?= number_format($p_compra, 2, ',', '.') ?></td>
                    <td style="padding: 20px 15px; color: #94a3b8; text-align: right;">R$ <?= number_format($p_venda_base, 2, ',', '.') ?></td>
                    
                    <td style="padding: 20px 15px; text-align: center;">
                        <?php if ($desc > 0): ?>
                            <span style="color: #ff3333; font-weight: bold;"><?= $desc ?>%</span>
                        <?php else: ?>
                            <span style="color: #94a3b8;">-</span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 20px 15px; text-align: right; font-weight: bold; color: <?= $desc > 0 ? '#22c55e' : '#ffffff' ?>;">
                        R$ <?= number_format($p_venda_final, 2, ',', '.') ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8; font-size: 15px;">
                    Nenhum produto ou lote encontrado para esta categoria.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>