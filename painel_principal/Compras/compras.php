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

    <header class="topbar">
        <div class="top-left">
            <img src="../../Imagens/carrinho2.png" width="70" height="70" alt="Logo Carrinho">
            <h1>INVEX</h1>
        </div>
    </header>

    <div class="layout">

        <aside class="sidebar">
            <nav>
                <a href="../painel_principal.php">🏠 Home</a>
                <a href="../cadastro_produtos/cad_list_prods.php">📦 Produtos</a>
                <a href="../compras/compras.php">🛒 Compras</a>
                <a href="../relatorios/buscar_relatorio.php">📊 Relatórios</a>
                <a>⚙️ Configurações</a>
            </nav>

            <a href="../../index.html" class="logout">🚪 Sair</a>
        </aside>

        <main class="main">

            <h1>Compras Coletivas</h1>

            <div class="produtos">

                <?php foreach($produtos as $produto){ ?>

                    <div class="card">

                        <img src="<?= $produto['imagemProduto'] ?>" alt="<?= $produto['nomeProduto'] ?>">

                        <h3><?= $produto['nomeProduto'] ?></h3>

                        <p>Marca: <?= $produto['marcaProduto'] ?></p>

                        <p><?= $produto['descricaoProduto'] ?></p>

                        <p>Quantidade disponível: <?= $produto['quantidade'] ?></p>

                        <p>Participantes: <?= $produto['quantidadeParticipantes'] ?> / <?= $produto['meta'] ?></p>

                        <div class="barra">
                            <div class="progresso" style="width: <?= ($produto['quantidadeParticipantes'] / $produto['meta']) * 100 ?>%;"></div>
                        </div>

                        <br>

                        <?php if($produto['status'] == 'Aberta'){ ?>
                            <span class="status-aberta">Aberta</span>
                        <?php } elseif($produto['status'] == 'Aguardando outro participante'){ ?>
                            <span class="status-aberta">Aguardando outro participante</span>
                        <?php } elseif($produto['status'] == 'Concluida'){ ?>
                            <span class="status-fechada">Concluída</span>
                        <?php } else { ?>
                            <span class="status-fechada">Cancelada</span>
                        <?php } ?>

                        <br><br>

                        <?php if($produto['status'] != 'Concluida' && $produto['status'] != 'Cancelada'){ ?>
                            <button onclick="participar(<?= $produto['idItem'] ?>)">Participar</button>
                        <?php } else { ?>
                            <button disabled>Compra indisponível</button>
                        <?php } ?>

                    </div>

                <?php } ?>

            </div>

        </main>

    </div>

    <script src="compras.js"></script>

</body>
</html>