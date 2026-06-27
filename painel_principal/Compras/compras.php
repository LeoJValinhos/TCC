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
                <a href="../cadastro_produtos/cad_list_prods.php">➡️​ Entrada</a>
                <a href="../saida/saida.php"> ⬅️​ Saida</a>
                <a href="../consulta/consulta.php"> 📦 Consulta</a>
                <a href="compras.php">🛒 Compras</a>
                <a href="../relatorios/buscar_relatorio.php">📊 Relatórios</a>
                <a href="../configuracoes/painel_principal_config.php">⚙️ Configurações</a>
            </nav>

            <a href="../../index.html" class="logout">🚪 Sair</a>
        </aside>

        <main class="main">

            <h1 class="nome-aba">Compras coletivas</h1>

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

                       <button class="btn-detalhes" onclick="abrirModal(<?= $produto['idItem'] ?>)">Ver Detalhes</button>

                    </div>

                <?php } ?>

            </div>

        </main>

    </div>
    <div id="modalCompra" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="fechar-modal" onclick="fecharModal()">&times;</span>
            
            <div class="modal-img">
                <img id="modalImagem" src="" alt="Produto">
            </div>
            
            <div class="modal-info">
                <h2 id="modalTitulo">Carregando...</h2>
                <p id="modalMarca"></p>
                <p id="modalDescricao"></p>
                <br>
                <div class="modal-precos">
                    <p><strong>Preço Total:</strong> <span>R$ 0,00</span> <small>(Futuro)</small></p>
                    <p><strong>Preço Unitário:</strong> <span>R$ 0,00</span> <small>(Futuro)</small></p>
                </div>
                <br>
                <h3>Participantes (<span id="modalQtdPart">0</span>/2):</h3>
                <ul class="participantes-lista" id="listaParticipantes">
                    </ul>
                
                <div class="modal-acoes">
                    <button id="btnParticiparModal" class="btn-participar">Participar</button>
                    <button id="btnCancelarModal" class="btn-cancelar">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="compras.js"></script>

</body>
</html>