<?php
// 1. Ativa a exibição de erros na tela para o TCC pegar qualquer falha de banco
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Inclui os arquivos de validação e banco de dados
include '../../funcoes/verifica_login.php';
include '../../funcoes/conexao.php';

// 3. Garante que a sessão está ativa e pega o ID da empresa logada
if (!isset($_SESSION)) {
    session_start();
}

// Verifica se as variáveis vindas do 'verifica_login.php' existem, senão busca da sessão
$idEmpresa = isset($_SESSION['idEmpresa']) ? $_SESSION['idEmpresa'] : null;
$nomeUsuario = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : "Usuário";

if (!$idEmpresa) {
    die("Erro crítico: Empresa não identificada na sessão. Por favor, refaça o login.");
}

// =====================================================
// ETAPA 1: ATUALIZAÇÃO AUTOMÁTICA DE LOTES VENCIDOS (CORRIGIDO)
// =====================================================
include 'funcoes/atualizar_status_lotes.php';

// 4. Captura o tipo de relatório pela URL, se não houver, assume o dashboard
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : "dashboard";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../painel_principal.css">
    <link rel="stylesheet" href="css/relatorios.css"> 
    
    <link rel="icon" type="image/png" href="../../Imagens/Carrinho.png" width="70" height="70">
    <title>INVEX - Relatórios</title>
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
                <a href="../compras/compras.php">🛒​ Compras</a>
                <a href="buscar_relatorio.php?tipo=dashboard">📊 Relatórios</a>
                <a>⚙️ Configurações</a>
            </nav>
            <a href="../../index.html" class="logout">🚪 Sair</a>
        </aside>

        <main class="main">

            <div class="top">
                <h2>Módulo de Relatórios Gerenciais</h2>
                <p class="subtitulo">Selecione uma categoria abaixo para visualizar os dados da cantina</p>
            </div>

            <div class="menu-relatorios" style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="?tipo=dashboard" style="padding: 10px 15px; background: #1a233a; color: #fff; text-decoration: none; border-radius: 5px;">Dashboard</a>
                    <a href="?tipo=produtos" style="padding: 10px 15px; background: #1a233a; color: #fff; text-decoration: none; border-radius: 5px;">Produtos</a>
                    <a href="?tipo=lotes" style="padding: 10px 15px; background: #1a233a; color: #fff; text-decoration: none; border-radius: 5px;">Lotes</a>
                    <a href="?tipo=vencimento" style="padding: 10px 15px; background: #1a233a; color: #fff; text-decoration: none; border-radius: 5px;">Vencimento</a>
                    <a href="?tipo=descontos" style="padding: 10px 15px; background: #1a233a; color: #fff; text-decoration: none; border-radius: 5px;">Descontos</a>
                    <a href="?tipo=lucro" style="padding: 10px 15px; background: #1a233a; color: #fff; text-decoration: none; border-radius: 5px;">Lucro</a>
                </div>

                <?php if ($tipo !== 'dashboard'): ?>
                <div style="display: flex; gap: 10px;">
                    <a href="gerar_excel.php?tipo=<?= $tipo ?>" class="btn" style="background: #22c55e; color: #fff; text-decoration: none; font-size: 14px; padding: 10px 15px;">📥 Baixar Excel</a>
                    <a href="gerar_pdf.php?tipo=<?= $tipo ?>" class="btn" target="_blank" style="background: #ef4444; color: #fff; text-decoration: none; font-size: 14px; padding: 10px 15px;">📄 Baixar PDF</a>
                </div>
                <?php endif; ?>
            </div>

            <hr style="border: 0; height: 1px; background: #333; margin-bottom: 20px;">

            <div class="conteudo-dinamico-relatorio">
                <?php
                switch($tipo){
                    case "produtos":
                        include "includes/relatorio_produtos.php";
                        break;
                    case "lotes":
                        include "includes/relatorio_lotes.php";
                        break;
                    case "vencimento":
                        include "includes/relatorio_vencimento.php";
                        break;
                    case "descontos":
                        include "includes/relatorio_descontos.php";
                        break;
                    case "lucro":
                        include "includes/relatorio_lucro.php";
                        break;
                    case "dashboard":
                    default:
                        include "relatorios.php";
                        break;
                }
                ?>
            </div>

        </main>

    </div>

    <script src="relatorios.js"></script>

</body>
</html>