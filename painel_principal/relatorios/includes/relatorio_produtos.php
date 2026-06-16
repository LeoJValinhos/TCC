<?php
// Busca os produtos cadastrados da empresa
$sql = "SELECT NomeProduto, MarcaProduto, preco_padrao_compra, preco_padrao_venda 
        FROM produtos 
        WHERE idEmpresa = $idEmpresa 
        ORDER BY NomeProduto ASC";
$result = $conn->query($sql);
?>

<div class="top" style="margin-bottom: 20px;">
    <h2>Relatório de Produtos Cadastrados</h2>
    <p class="subtitulo">Listagem geral de produtos e preços base</p>
</div>

<table style="width: 100%; border-collapse: collapse; background: var(--card); border-radius: 10px; overflow: hidden; border: 1px solid var(--border);">
    <thead>
        <tr style="background: rgba(0, 245, 212, 0.1); text-align: left; color: var(--primary);">
            <th style="padding: 15px;">Produto</th>
            <th style="padding: 15px;">Marca</th>
            <th style="padding: 15px;">Preço Compra</th>
            <th style="padding: 15px;">Preço Venda</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid rgba(0, 183, 195, 0.1); color: var(--text);">
                    <td style="padding: 15px;"><?= htmlspecialchars($row['NomeProduto']) ?></td>
                    <td style="padding: 15px;"><?= htmlspecialchars($row['MarcaProduto']) ?></td>
                    <td style="padding: 15px;">R$ <?= number_format($row['preco_padrao_compra'], 2, ',', '.') ?></td>
                    <td style="padding: 15px;">R$ <?= number_format($row['preco_padrao_venda'], 2, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="padding: 20px; text-align: center; color: var(--sub);">Nenhum produto cadastrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>