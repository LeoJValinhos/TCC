<?php
require_once("produtos_simulado.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Compras coletivas</title>
    <link rel="stylesheet" href="compras.css">
</head>
<body>

<h1>Compras coletivas</h1>

<div class="produtos">

<?php foreach($produtos as $produto){ ?>

    <div class="card">

        <img src="<?= $produto['imagem'] ?>">

        <h3><?= $produto['nome'] ?></h3>

        <p class="preco">
            R$ <?= number_format($produto['preco'], 2, ",", ".") ?>
        </p>

        <p>
            Participantes:
            <?= $produto['participantes'] ?>
            /
            <?= $produto['meta'] ?>
        </p>

        <div class="barra">
            <div
            class="progresso"
            style="
            width: <?= ($produto['participantes'] / $produto['meta']) * 100 ?>%;
            ">
            </div>
        </div>

        <br>

        <?php if($produto['status'] == 'aberta'){ ?>

            <span class="status-aberta">
                Aberta
            </span>

        <?php } else { ?>

            <span class="status-fechada">
                Fechada
            </span>

        <?php } ?>

        <br><br>

        <?php if($produto['participantes'] < $produto['meta']){ ?>

            <button
            onclick="participar(<?= $produto['id'] ?>)">
                Participar
            </button>

        <?php } else { ?>

            <button disabled>
                Compra fechada
            </button>

        <?php } ?>

    </div>

<?php } ?>

</div>

<script src="compras.js"></script>

</body>
</html>