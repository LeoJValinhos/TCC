
<?php
require_once 'cad_list_prods_dados.php';

/* =====================================================
BUSCAR PRODUTOS PARA O SELECT DE LOTES
===================================================== */
$produtos_select = $conn->query("
    SELECT
        idProduto,
        NomeProduto,
        MarcaProduto
    FROM produtos
    WHERE idEmpresa = $idEmpresa
    ORDER BY NomeProduto ASC
");
?>
<?php include_once '../topo_notificacoes.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cad_list_prods.css">
    <link rel="icon" type="image/png" href="../../Imagens/Carrinho.png">
    <title>INVEX - Cadastro de produtos</title>

    <style>
        .vermelho-validade {
            background-color: #ffb3b3;
        }

        .amarelo-validade {
            background-color: #fff0a6;
        }

        .paginacao a {
            padding: 6px 12px;
            background: #222;
            color: white;
            text-decoration: none;
            margin: 2px;
            border-radius: 5px;
        }

        .paginacao a:hover {
            background: #444;
        }
    </style>
</head>

<body>

    <!-- TOPO (FIXO) -->
    <header class="topbar">
        <div class="top-left">
            <img src="../../imagens/carrinho2.png" width="70" height="70" alt="Logo Carrinho">
            <h1>INVEX</h1>
        </div>
    </header>

    <div class="layout">

        <!-- SIDEBAR (TRAVADA NA TELA) -->
        <aside class="sidebar">
            <nav>
                <a href="../painel_principal.php">🏠 Home</a>
                <a href="cad_list_prods.php">➡️​ Entrada</a>
                <a href="../saida/saida.php"> ⬅️​ Saida</a>
                <a href="../consulta/consulta.php"> 📦 Consulta</a>
                <a href="../compras/compras.php">🛒 Compras</a>
                <a href="../relatorios/buscar_relatorio.php">📊 Relatórios</a>
                <a href="../configuracoes/painel_principal_config.php">⚙️ Configurações</a>
            </nav>

            <a href="../../index.html" class="logout">🚪 Sair</a>
        </aside>

        <!-- CONTEÚDO PRINCIPAL (ROLA INDEPENDENTE) -->
        <main class="main">
            <div class="container">

                <h2>Sistema de entrada de produtos e lotes</h2>
                <p class="usuario">Aqui é para registrar produtos, e os lotes dos produtos já cadastrados. </p>

                <p class="usuario">
                    Usuário logado:
                    <b><?= htmlspecialchars($_SESSION['nome']) ?></b>
                </p>

                <div class="forms-grid">

                    <!-- CADASTRO DE PRODUTOS -->
                    <div class="form-card">
                        <h3>Cadastro de itens</h3>
                        <form method="POST" action="">
                            <label>Nome do produto</label>
                            <input type="text" name="nome_produto" required>

                            <label>Marca</label>
                            <input type="text" name="marca" required>

                            <label>Descrição</label>
                            <textarea name="descricao"></textarea>

                            <label>Preço padrão de compra (R$)</label>
                            <input type="number" name="preco_padrao_compra" step="0.01" min="0" required>

                            <label>Preço padrão de venda (R$)</label>
                            <input type="number" name="preco_padrao_venda" step="0.01" min="0" required>

                            <label>Estoque mínimo</label>
                            <input type="number" name="estoque_minimo" min="0" required>

                            <input type="submit" name="cadastrar_produto" value="Cadastrar produto">
                        </form>
                    </div>

                    <!-- CADASTRO DE LOTES -->
                    <div class="form-card">
                        <h3>Cadastrar lote</h3>
                        <form method="POST" action="">
                            <label>Produto</label>
                            <select name="idproduto" required>
                                <option value="">Selecione um produto</option>
                                <?php while($produto = $produtos_select->fetch_assoc()){ ?>
                                    <option value="<?= $produto['idProduto'] ?>">
                                      <?= htmlspecialchars($produto['NomeProduto']) ?> 
                                      <?= htmlspecialchars($produto['MarcaProduto']) ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <label>Quantidade</label>
                            <input type="number" name="quantidade" min="1" required>

                            <label>Validade</label>
                            <input type="date" name="validade" required>

                            <label>Preço de compra do lote (R$)</label>
                            <input type="number" name="preco_compra" step="0.01" min="0" required>

                            <label>Preço de venda do lote (R$)</label>
                            <input type="number" name="preco_venda" step="0.01" min="0" required>

                            <label>Desconto (%)</label>
                            <input type="number" name="desconto" step="0.01" min="0" max="100" value="0">

                            <input type="submit" name="cadastrar_lote" value="Cadastrar lote">
                        </form>
                    </div>

                </div>

            </div>
            <br>
        </main>

    </div>

    <script src="cad_list_prods.js"></script>

</body>
</html>