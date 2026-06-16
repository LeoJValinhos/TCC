<?php
// Busca lotes trazendo os mais próximos de vencer primeiro
$sql = "SELECT l.numero_lote, l.quantidade, l.validade, l.status_lote, p.NomeProduto 
        FROM produtoslotes l
        INNER JOIN produtos p ON p.idProduto = l.idproduto
        WHERE l.idEmpresa = $idEmpresa AND l.validade IS NOT NULL
        ORDER BY l.validade ASC";
$result = $conn->query($sql);
?>

<div class="top" style="margin-bottom: 20px;">
    <h2>Relatório de Controle de Vencimentos</h2>
    <p class="subtitulo">Acompanhamento prioritário de validades (Crítico para a Cantina)</p>
</div>

<table style="width: 100%; border-collapse: collapse; background: var(--card); border-radius: 10px; overflow: hidden; border: 1px solid var(--border);">
    <thead>
        <tr style="background: rgba(0, 245, 212, 0.1); text-align: left; color: var(--primary);">
            <th style="padding: 15px;">Produto</th>
            <th style="padding: 15px;">Nº Lote</th>
            <th style="padding: 15px;">Qtd</th>
            <th style="padding: 15px;">Data de Vencimento</th>
            <th style="padding: 15px;">Situação</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): 
                $hoje = date('Y-m-d');
                $cssStatus = "#22c55e"; 
                $txtStatus = "Ok";

                if ($row['validade'] < $hoje) {
                    $cssStatus = "#ef4444";
                    $txtStatus = "VENCIDO";
                } elseif (strtotime($row['validade']) <= strtotime('+15 days')) {
                    $cssStatus = "#eab308";
                    $txtStatus = "ALERTA (Prestes a vencer)";
                }
            ?>
                <tr style="border-bottom: 1px solid rgba(0, 183, 195, 0.1); color: var(--text);">
                    <td style="padding: 15px;"><?= htmlspecialchars($row['NomeProduto']) ?></td>
                    <td style="padding: 15px;"><?= htmlspecialchars($row['numero_lote']) ?></td>
                    <td style="padding: 15px;"><?= $row['quantidade'] ?> un</td>
                    <td style="padding: 15px; font-weight: bold;"><?= date('d/m/Y', strtotime($row['validade'])) ?></td>
                    <td style="padding: 15px;">
                        <span style="color: <?= $cssStatus ?>; font-weight: bold; font-size: 13px;">
                            ⚠️ <?= $txtStatus ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="padding: 20px; text-align: center; color: var(--sub);">Nenhum dado de validade disponível.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>