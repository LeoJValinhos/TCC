<!DOCTYPE html>
<html lang="pt-br">
<head>

<meta charset="UTF-8">

<title>Compras</title>

<link rel="stylesheet" href="compras.css">

</head>

<body>

<div class="container">

    <header>

        <h1>Compras coletivas</h1>

        <p>Junte-se a outras empresas e economize.</p>

    </header>

    <section class="resumo">

        <div class="box">
            <h2>12</h2>
            <span>Compras abertas</span>
        </div>

        <div class="box">
            <h2>45</h2>
            <span>Participantes</span>
        </div>

        <div class="box">
            <h2>18%</h2>
            <span>Economia média</span>
        </div>

    </section>

    <section class="filtros">

        <input
        type="text"
        id="busca"
        placeholder="Buscar produto">

    </section>

    <section
    id="listaProdutos"
    class="produtos">

    </section>

</div>

<script src="compras.js"></script>

</body>
</html>