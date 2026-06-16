<?php
// Busca lotes que possuem descontos aplicados
$sql = "SELECT l.numero_lote, l.quantidade, l.desconto, p.NomeProduto 
        FROM produtoslotes l
        INNER JOIN produtos p ON p.idProduto = l.idproduto
        WHERE l.idEmpresa = $idEmpresa AND l.desconto > 0
        ORDER BY l.desconto DESC";
$result = $conn->query($sql);
?>

<div class="top" style="margin-bottom: 20px;">
    <h2>Relatório de Lotes com Desconto</h2>
    <p class="subtitulo">Produtos com margem promocional ativa</p>
</div>

<table style="width: 100%; border-collapse: collapse; background: var(--card); border-radius: 10px; overflow: hidden; border: 1px solid var(--border);">
    <thead>
        <tr style="background: rgba(0, 245, 212, 0.1); text-align: left; color: var(--primary);">
            <th style="padding: 15px;">Produto</th>
            <th style="padding: 15px;">Nº Lote</th>
            <th style="padding: 15px;">Quantidade</th>
            <th style="padding: 15px;">Desconto Aplicado</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid rgba(0, 183, 195, 0.1); color: var(--text);">
                    <td style="padding: 15px;"><?= htmlspecialchars($row['NomeProduto']) ?></td>
                    <td style="padding: 15px;"><?= htmlspecialchars($row['numero_lote']) ?></td>
                    <td style="padding: 15px;"><?= $row['quantidade'] ?> un</td>
                    <td style="padding: 15px; color: #00F5D4; font-weight: bold;"><?= number_format($row['desconto'], 0) ?>% OFF</td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center; color: var(--sub);">Não há lotes com desconto ativo no momento.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>