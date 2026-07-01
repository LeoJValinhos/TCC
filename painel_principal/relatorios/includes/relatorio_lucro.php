<?php
// Calcula a diferença entre preço de venda e compra para projetar margem
$sql = "SELECT NomeProduto, preco_padrao_compra, preco_padrao_venda,
        (preco_padrao_venda - preco_padrao_compra) AS lucro_unitario
        FROM produtos 
        WHERE idEmpresa = $idEmpresa
        ORDER BY lucro_unitario DESC";
$result = $conn->query($sql);
?>

<div class="top" style="margin-bottom: 20px;">
    <h2>Relatório de Estimativa de Margem de Lucro</h2>
    <p class="subtitulo">Análise comparativa estática entre valores de compra e venda</p>
</div>

<table style="width: 100%; border-collapse: collapse; background: var(--card); border-radius: 10px; overflow: hidden; border: 1px solid var(--border);">
    <thead>
        <meta>
        <tr style="background: rgba(0, 245, 212, 0.1); text-align: left; color: var(--primary);">
            <th style="padding: 15px;">Produto</th>
            <th style="padding: 15px;">Custo Médio</th>
            <th style="padding: 15px;">Preço de Venda</th>
            <th style="padding: 15px;">Lucro Unitário Estimado</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid rgba(0, 183, 195, 0.1); color: var(--text);">
                    <td style="padding: 15px;"><?= htmlspecialchars($row['NomeProduto']) ?></td>
                    <td style="padding: 15px; color: #ef4444;">R$ <?= number_format($row['preco_padrao_compra'], 2, ',', '.') ?></td>
                    <td style="padding: 15px; color: #22c55e;">R$ <?= number_format($row['preco_padrao_venda'], 2, ',', '.') ?></td>
                    <td style="padding: 15px; color: #00F5D4; font-weight: bold;">R$ <?= number_format($row['lucro_unitario'], 2, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center; color: var(--sub);">Dados insuficientes para calcular margem.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>