<?php
// Busca os lotes e os nomes dos produtos associados
$sql = "SELECT l.numero_lote, l.quantidade, l.validade, l.status_lote, p.NomeProduto 
        FROM produtoslotes l
        INNER JOIN produtos p ON p.idProduto = l.idproduto
        WHERE l.idEmpresa = $idEmpresa
        ORDER BY p.NomeProduto ASC";
$result = $conn->query($sql);
?>

<div class="top" style="margin-bottom: 20px;">
    <h2>Relatório Geral de Lotes em Estoque</h2>
    <p class="subtitulo">Controle de quantidades físicas e códigos de lotes</p>
</div>

<table style="width: 100%; border-collapse: collapse; background: var(--card); border-radius: 10px; overflow: hidden; border: 1px solid var(--border);">
    <thead>
        <tr style="background: rgba(0, 245, 212, 0.1); text-align: left; color: var(--primary);">
            <th style="padding: 15px;">Produto</th>
            <th style="padding: 15px;">Nº Lote</th>
            <th style="padding: 15px;">Quantidade</th>
            <th style="padding: 15px;">Validade</th>
            <th style="padding: 15px;">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid rgba(0, 183, 195, 0.1); color: var(--text);">
                    <td style="padding: 15px;"><?= htmlspecialchars($row['NomeProduto']) ?></td>
                    <td style="padding: 15px;"><?= htmlspecialchars($row['numero_lote']) ?></td>
                    <td style="padding: 15px;"><?= $row['quantidade'] ?> un</td>
                    <td style="padding: 15px;"><?= $row['validade'] ? date('d/m/Y', strtotime($row['validade'])) : 'Não informada' ?></td>
                    <td style="padding: 15px;">
                        <span style="padding: 4px 8px; border-radius: 5px; font-size: 12px; font-weight: bold; background: <?= $row['status_lote'] == 'vencido' ? '#ef4444' : ($row['status_lote'] == 'promocao' ? '#eab308' : '#22c55e') ?>; color: #fff;">
                            <?= strtoupper($row['status_lote']) ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="padding: 20px; text-align: center; color: var(--sub);">Nenhum lote encontrado no estoque.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>