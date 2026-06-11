<?php
require_once("produtos_simulado.php");
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        Compras coletivas
    </title>

    <link rel="stylesheet" href="compras.css">

</head>

<body>

<h1>
    Compras coletivas
</h1>

<div class="produtos">

<?php foreach($produtos as $produto){ ?>

    <div class="card">

        <img
        src="<?= $produto['imagemProduto'] ?>"
        alt="<?= $produto['nomeProduto'] ?>">

        <h3>
            <?= $produto['nomeProduto'] ?>
        </h3>

        <p>
            Marca:
            <?= $produto['marcaProduto'] ?>
        </p>

        <p>
            <?= $produto['descricaoProduto'] ?>
        </p>

        <p>
            Quantidade disponível:
            <?= $produto['quantidade'] ?>
        </p>

        <p>
            Participantes:
            <?= $produto['quantidadeParticipantes'] ?>
            /
            <?= $produto['meta'] ?>
        </p>

        <div class="barra">

            <div
            class="progresso"

            style="
            width:
            <?= ($produto['quantidadeParticipantes'] / $produto['meta']) * 100 ?>%;
            ">

            </div>

        </div>

        <br>

        <?php if($produto['status'] == 'Aberta'){ ?>

            <span class="status-aberta">
                Aberta
            </span>

        <?php } elseif($produto['status'] == 'Aguardando outro participante'){ ?>

            <span class="status-aberta">
                Aguardando outro participante
            </span>

        <?php } elseif($produto['status'] == 'Concluida'){ ?>

            <span class="status-fechada">
                Concluída
            </span>

        <?php } else { ?>

            <span class="status-fechada">
                Cancelada
            </span>

        <?php } ?>

        <br><br>

        <?php if(
            $produto['status'] != 'Concluida'
            &&
            $produto['status'] != 'Cancelada'
        ){ ?>

            <button
            onclick="participar(<?= $produto['idItem'] ?>)">

                Participar

            </button>

        <?php } else { ?>

            <button disabled>

                Compra indisponível

            </button>

        <?php } ?>

    </div>

<?php } ?>

</div>

<script src="compras.js"></script>

</body>

</html>