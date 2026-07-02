-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 02-Jul-2026 às 02:32
-- Versão do servidor: 5.7.36
-- versão do PHP: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `databasetcc`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `alertas_ocultos`
--

CREATE TABLE `alertas_ocultos` (
  `id_oculto` int(11) NOT NULL,
  `idEmpresa` int(11) NOT NULL,
  `idProduto` int(11) DEFAULT NULL,
  `numero_lote` varchar(50) DEFAULT NULL,
  `tipo_alerta` varchar(20) NOT NULL,
  `data_ocultado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cadastros`
--

CREATE TABLE `cadastros` (
  `idCadastro` int(11) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `sobrenome` varchar(60) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `email` varchar(60) NOT NULL,
  `datanasc` date NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `celular` varchar(11) NOT NULL,
  `idEmpresa` int(11) DEFAULT NULL,
  `tipocadastro` enum('EMPRESA/ADM','FUNCIONARIO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `cadastros`
--

INSERT INTO `cadastros` (`idCadastro`, `nome`, `sobrenome`, `senha`, `email`, `datanasc`, `cpf`, `celular`, `idEmpresa`, `tipocadastro`) VALUES
(7, 'Vinicius', 'Sales', '12345678', 'vini@gmail.com', '2006-01-27', '78787878787', '78788787787', 6, 'EMPRESA/ADM'),
(8, 'Leonardo', 'Valinhos', '12345678', 'Leo@gmail.com', '2007-03-20', '77987897899', '78899878979', 7, 'EMPRESA/ADM'),
(9, 'Kaue', 'Silva', '12345678', 'kaue@gmail.com', '2000-07-15', '74787884112', '23542653246', 7, 'FUNCIONARIO'),
(10, 'Oscar', 'Alhos', '12345678', 'Oscar@gmail.com', '2000-04-20', '12184185561', '23143214124', 7, 'FUNCIONARIO');

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes_alertas`
--

CREATE TABLE `configuracoes_alertas` (
  `id_config` int(11) NOT NULL,
  `idEmpresa` int(11) NOT NULL,
  `dias_antecedencia_vencimento` int(11) DEFAULT '30'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes_gerais`
--

CREATE TABLE `configuracoes_gerais` (
  `id_config` int(11) NOT NULL,
  `idEmpresa` int(11) NOT NULL,
  `alerta_email` tinyint(1) NOT NULL DEFAULT '0',
  `alerta_login` tinyint(1) NOT NULL DEFAULT '1',
  `som_alerta` tinyint(1) NOT NULL DEFAULT '1',
  `casas_decimais` int(11) NOT NULL DEFAULT '2',
  `simbolo_moeda` varchar(10) NOT NULL DEFAULT 'R$',
  `formato_data` varchar(20) NOT NULL DEFAULT 'd/m/Y',
  `cor_primaria` varchar(20) NOT NULL DEFAULT '#00d9ff',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `configuracoes_gerais`
--

INSERT INTO `configuracoes_gerais` (`id_config`, `idEmpresa`, `alerta_email`, `alerta_login`, `som_alerta`, `casas_decimais`, `simbolo_moeda`, `formato_data`, `cor_primaria`, `criado_em`, `atualizado_em`) VALUES
(3, 7, 0, 1, 1, 2, 'R$', 'd/m/Y', '#00d9ff', '2026-07-02 02:25:43', '2026-07-02 02:25:43');

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
--

CREATE TABLE `empresa` (
  `idEmpresa` int(11) NOT NULL,
  `nomeEmpresa` varchar(300) NOT NULL,
  `CNPJ` char(14) NOT NULL,
  `codigoEmpresa` varchar(7) NOT NULL,
  `codigoADM` varchar(7) NOT NULL,
  `idAdm` int(11) DEFAULT NULL,
  `nomeAdm` varchar(30) DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`idEmpresa`, `nomeEmpresa`, `CNPJ`, `codigoEmpresa`, `codigoADM`, `idAdm`, `nomeAdm`, `criado_em`) VALUES
(6, 'Sales Corp', '87978979898978', '8048829', '8378707', 7, 'Vinicius', '2026-07-01 23:12:08'),
(7, 'leleco interpraise', '97987987979878', '9510891', '7307961', 8, 'Leonardo', '2026-07-01 23:13:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `loja_virtual`
--

CREATE TABLE `loja_virtual` (
  `idItem` int(11) NOT NULL,
  `nomeProduto` varchar(100) NOT NULL,
  `marcaProduto` varchar(255) NOT NULL,
  `descricaoProduto` varchar(255) DEFAULT NULL,
  `quantidade` varchar(11) NOT NULL,
  `imagemProduto` varchar(255) NOT NULL,
  `meta` int(11) DEFAULT '2',
  `quantidadeParticipantes` int(11) DEFAULT '0',
  `status` enum('Aberta','Aguardando outro participante','Concluida','Cancelada') DEFAULT 'Aberta',
  `fornecedor` varchar(150) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valor_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descontopor_quantidade_produto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantidade_deproduto_minimo_desconto` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Extraindo dados da tabela `loja_virtual`
--

INSERT INTO `loja_virtual` (`idItem`, `nomeProduto`, `marcaProduto`, `descricaoProduto`, `quantidade`, `imagemProduto`, `meta`, `quantidadeParticipantes`, `status`, `fornecedor`, `valor_unitario`, `valor_total`, `descontopor_quantidade_produto`, `quantidade_deproduto_minimo_desconto`) VALUES
(55, 'Sabão em Pó', 'Brilhante', 'Sabão em pó para limpeza profunda', '200', '../../imagens/sabao_brilho.png', 2, 1, 'Aguardando outro participante', 'Distribuidora Alfa Ltda', '12.50', '1250.00', '0.00', 0),
(56, 'Amaciante de Roupas', 'Soft', 'Amaciante concentrado 1L', '150', '../../imagens/amaciante_soft.png', 2, 1, 'Aguardando outro participante', 'Atacadista Central', '4.99', '499.00', '0.50', 50),
(57, 'Detergente Líquido', 'Limpex', 'Detergente neutro para louças 500ml', '300', '../../imagens/detergente_limpex.png', 3, 0, 'Aberta', 'Tech Importados S.A.', '89.90', '4495.00', '5.00', 20),
(58, 'Desinfetante Pinho', 'Força', 'Desinfetante uso geral 1L', '120', '../../imagens/desinfetante_forca.png', 2, 0, 'Aberta', 'Global Alimentos', '3.50', '700.00', '0.00', 0),
(59, 'Água Sanitária', 'Pura', 'Água sanitária para desinfecção 1L', '180', '../../imagens/agua_sanitaria_pura.png', 2, 1, 'Aguardando outro participante', 'RM Logística e Transportes', '150.00', '3000.00', '15.00', 10),
(60, 'Esponja de Aço', 'Brilhus', 'Pacote com 4 unidades', '250', '../../imagens/esponja_brilhante.png', 4, 0, 'Aberta', 'Comercial Silva', '8.25', '825.00', '0.25', 100),
(61, 'Limpador Multiuso', 'Uau', 'Limpador spray multiuso 500ml', '140', '../../imagens/limpador_acao.png', 2, 1, 'Aguardando outro participante', 'Brasil Bebidas Distribuição', '6.00', '1200.00', '0.00', 0),
(62, 'Saco de Lixo 50L', 'DoverRoll', 'Pacote com 10 unidades', '160', '../../imagens/saco_lixo_resiste.png', 2, 0, 'Aberta', 'Nova Era Cosméticos', '22.40', '1120.00', '2.40', 30),
(63, 'Arroz Integral', 'Grão de ouro', 'Pacote de arroz integral 1kg', '90', '../../imagens/arroz_grao_ouro.png', 2, 2, 'Concluida', 'Indústria Sol Nascente', '45.00', '2250.00', '0.00', 0),
(64, 'Feijão Carioca', 'Dona Dê', 'Feijão carioca tipo 1 1kg', '110', '../../imagens/feijao_dona_benta.png', 2, 0, 'Aberta', 'Prime Suprimentos', '18.00', '900.00', '1.50', 40),
(65, 'Macarrão Espaguete', 'Amália', 'Macarrão sêmola espaguete 500g', '210', '../../imagens/macarrao_massa_boa.png', 3, 0, 'Aberta', 'JP Hortifruti Organizações', '2.30', '460.00', '0.00', 0),
(66, 'Óleo de Soja', 'Soya', 'Óleo de soja garrafa 900ml', '170', '../../imagens/oleo_leve.png', 2, 0, 'Aberta', 'Master Limpeza Profissional', '14.90', '1490.00', '1.90', 50),
(67, 'Açúcar Refinado', 'União', 'Açúcar refinado pacote 1kg', '130', '../../imagens/acucar_doce_vida.png', 2, 1, 'Aguardando outro participante', 'Eletro Mundo Distribuidora', '299.90', '5998.00', '20.00', 5),
(68, 'Sal Refinado', 'Cisne', 'Sal refinado de cozinha 1kg', '80', '../../imagens/sal_iodado.png', 4, 0, 'Aberta', 'Distribuidora Vale do Rio', '5.50', '550.00', '0.00', 0),
(69, 'Café Torrado e Moído', 'Pilão', 'Café a vácuo tradicional 500g', '140', '../../imagens/cafe_aroma.png', 2, 0, 'Aberta', 'Sul Medicamentos S.A.', '34.00', '3400.00', '4.00', 100),
(70, 'Farinha de Trigo', 'Dona Benta', 'Farinha de trigo tipo 1 1kg', '100', '../../imagens/farinha_premium.png', 2, 0, 'Aberta', 'Ferramentas Forte Ltda', '65.00', '1300.00', '5.00', 15),
(71, 'Achocolatado em Pó', 'Nescau', 'Lata de achocolatado 400g', '125', '../../imagens/achocolatado_chocomax.png', 2, 0, 'Aberta', 'Papelaria VIP Atacado', '1.20', '600.00', '0.10', 200),
(72, 'Cereal Matinal', 'Sucrilhos', 'Cereal de milho tradicional 300g', '85', '../../imagens/cereal_nutricroc.png', 2, 0, 'Aberta', 'Construtora e Materiais Base', '110.00', '5500.00', '0.00', 0),
(73, 'Biscoito Recheado', 'Passatempo', 'Biscoito sabor chocolate 130g', '400', '../../imagens/biscoito_doce_mania.png', 5, 0, 'Aberta', 'Uni Calçados Distribuição', '75.00', '3750.00', '7.50', 25),
(74, 'Biscoito Salgado', 'Marilan', 'Biscoito água e sal 350g', '190', '../../imagens/biscoito_crack.png', 3, 0, 'Aberta', 'Auto Peças Líder', '120.00', '2400.00', '10.00', 12),
(75, 'Geleia de Morango', 'Predilecta', 'Pote de geleia de morango 230g', '60', '../../imagens/geleia_fruta_pura.png', 2, 0, 'Aberta', 'Supermercado Real Atacadista', '7.80', '780.00', '0.00', 0),
(76, 'Torrada Tradicional', 'Schar', 'Pacote de torradas leves 150g', '95', '../../imagens/torrada_crocante.png', 2, 0, 'Aberta', 'Fast Fashion Confecções', '39.90', '1995.00', '4.00', 30),
(77, 'Leite em Pó', 'Itambé', 'Leite em pó integral sachê 400g', '115', '../../imagens/leite_nutrivida.png', 2, 0, 'Aberta', 'Macro Atacado Corp', '16.50', '1650.00', '1.50', 60),
(78, 'Aveia em Flocos', 'Yoki', 'Caixa de aveia em flocos finos 170g', '75', '../../imagens/aveia_natural.png', 3, 0, 'Aberta', 'Armazém Geral Nordeste', '9.90', '990.00', '0.00', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `participantes_loja`
--

CREATE TABLE `participantes_loja` (
  `idParticipacao` int(11) NOT NULL,
  `idItem` int(11) NOT NULL,
  `id_primeiroParticipante` int(11) DEFAULT NULL,
  `qtd_primeiroParticipante` int(11) DEFAULT NULL,
  `id_segundoParticipante` int(11) DEFAULT NULL,
  `qtd_segundoParticipante` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `participantes_loja`
--

INSERT INTO `participantes_loja` (`idParticipacao`, `idItem`, `id_primeiroParticipante`, `qtd_primeiroParticipante`, `id_segundoParticipante`, `qtd_segundoParticipante`) VALUES
(15, 55, 7, 1, NULL, NULL),
(16, 56, 7, 1, NULL, NULL),
(17, 59, 7, 1, NULL, NULL),
(18, 61, 7, 1, NULL, NULL),
(19, 63, 7, 1, 8, 250),
(20, 67, 7, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `idProduto` int(11) NOT NULL,
  `NomeProduto` varchar(100) NOT NULL,
  `MarcaProduto` varchar(100) NOT NULL,
  `Descricao` varchar(255) DEFAULT NULL,
  `idEmpresa` int(11) NOT NULL,
  `criadopor_nome` varchar(100) DEFAULT NULL,
  `criadoem` datetime DEFAULT CURRENT_TIMESTAMP,
  `criadopor_id` int(11) DEFAULT NULL,
  `preco_padrao_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `preco_padrao_venda` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estoque_minimo` int(11) NOT NULL DEFAULT '0',
  `estoque_minimo_original` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `NomeProduto`, `MarcaProduto`, `Descricao`, `idEmpresa`, `criadopor_nome`, `criadoem`, `criadopor_id`, `preco_padrao_compra`, `preco_padrao_venda`, `estoque_minimo`, `estoque_minimo_original`) VALUES
(17, 'Amaciante', 'confort', '', 7, 'Leonardo', '2026-07-01 23:15:11', 8, '5.00', '10.00', 100, NULL),
(18, 'Amaciante', 'Ype', '', 7, 'Leonardo', '2026-07-01 23:15:54', 8, '8.00', '16.00', 100, NULL),
(19, 'Bolacha', 'Trakinas', '', 7, 'Leonardo', '2026-07-01 23:16:12', 8, '5.00', '8.00', 200, NULL),
(20, 'Maionese', 'Hellmans', 'pote de 300g', 7, 'Leonardo', '2026-07-01 23:16:56', 8, '10.00', '16.00', 50, NULL),
(21, 'Bolacha', 'Passatempo', '', 7, 'Leonardo', '2026-07-01 23:18:59', 8, '4.00', '8.00', 200, NULL),
(22, 'Biscoito', 'balduco', '', 7, 'Leonardo', '2026-07-01 23:20:46', 8, '8.00', '15.00', 70, NULL),
(23, 'Ketchup', 'Quero', '', 7, 'Leonardo', '2026-07-01 23:24:03', 8, '5.00', '9.00', 200, NULL),
(24, 'Mostarda', 'Quero', '400G', 7, 'Leonardo', '2026-07-01 23:28:51', 8, '5.00', '9.00', 100, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtoslotes`
--

CREATE TABLE `produtoslotes` (
  `idlote` int(11) NOT NULL,
  `idproduto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `validade` date DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `criadopor_id` int(11) NOT NULL,
  `criadopor_nome` varchar(255) DEFAULT NULL,
  `idEmpresa` int(11) DEFAULT NULL,
  `numero_lote` varchar(50) NOT NULL,
  `preco_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `preco_venda` decimal(10,2) NOT NULL DEFAULT '0.00',
  `desconto` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status_lote` enum('normal','promocao','vencido') NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `produtoslotes`
--

INSERT INTO `produtoslotes` (`idlote`, `idproduto`, `quantidade`, `validade`, `criado_em`, `criadopor_id`, `criadopor_nome`, `idEmpresa`, `numero_lote`, `preco_compra`, `preco_venda`, `desconto`, `status_lote`) VALUES
(13, 19, 480, '2026-07-20', '2026-07-01 23:17:44', 8, 'Leonardo', 7, 'LOTE-19', '5.00', '8.00', '0.00', 'normal'),
(14, 20, 10, '2026-07-02', '2026-07-01 23:18:17', 8, 'Leonardo', 7, 'LOTE-20', '10.00', '16.00', '0.00', 'normal'),
(15, 22, 500, '2026-06-28', '2026-07-01 23:21:05', 8, 'Leonardo', 7, 'LOTE-22', '7.00', '16.00', '0.00', 'vencido'),
(16, 17, 150, '2028-01-20', '2026-07-01 23:21:43', 8, 'Leonardo', 7, 'LOTE-17', '10.00', '20.00', '0.00', 'normal'),
(17, 23, 10, '2028-02-07', '2026-07-01 23:24:23', 8, 'Leonardo', 7, 'LOTE-23', '4.00', '9.00', '0.00', 'normal'),
(18, 24, 150, '2029-06-30', '2026-07-01 23:29:15', 8, 'Leonardo', 7, 'LOTE-24', '6.00', '9.00', '0.00', 'normal');

-- --------------------------------------------------------

--
-- Estrutura da tabela `saida`
--

CREATE TABLE `saida` (
  `id_saida` int(11) NOT NULL,
  `idlote` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `criadopor_id` int(11) NOT NULL,
  `criadopor_nome` varchar(255) NOT NULL,
  `quantidade_saida` int(11) NOT NULL,
  `motivo_saida` varchar(255) NOT NULL,
  `data_saida` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `saida`
--

INSERT INTO `saida` (`id_saida`, `idlote`, `id_lote`, `criadopor_id`, `criadopor_nome`, `quantidade_saida`, `motivo_saida`, `data_saida`) VALUES
(56, 13, 13, 8, 'Leonardo', 20, 'Ajuste', '2026-07-01 23:29:36');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `alertas_ocultos`
--
ALTER TABLE `alertas_ocultos`
  ADD PRIMARY KEY (`id_oculto`);

--
-- Índices para tabela `cadastros`
--
ALTER TABLE `cadastros`
  ADD PRIMARY KEY (`idCadastro`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `celular` (`celular`),
  ADD KEY `fk_usuario_empresa` (`idEmpresa`);

--
-- Índices para tabela `configuracoes_alertas`
--
ALTER TABLE `configuracoes_alertas`
  ADD PRIMARY KEY (`id_config`);

--
-- Índices para tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  ADD PRIMARY KEY (`id_config`),
  ADD UNIQUE KEY `idEmpresa` (`idEmpresa`);

--
-- Índices para tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`idEmpresa`),
  ADD UNIQUE KEY `CNPJ` (`CNPJ`),
  ADD UNIQUE KEY `codigoEmpresa` (`codigoEmpresa`),
  ADD KEY `fk_adm` (`idAdm`);

--
-- Índices para tabela `loja_virtual`
--
ALTER TABLE `loja_virtual`
  ADD PRIMARY KEY (`idItem`);

--
-- Índices para tabela `participantes_loja`
--
ALTER TABLE `participantes_loja`
  ADD PRIMARY KEY (`idParticipacao`),
  ADD KEY `idItem` (`idItem`),
  ADD KEY `id_primeiroParticipante` (`id_primeiroParticipante`),
  ADD KEY `id_segundoParticipante` (`id_segundoParticipante`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`idProduto`),
  ADD UNIQUE KEY `unique_produto_empresa` (`NomeProduto`,`MarcaProduto`,`idEmpresa`),
  ADD KEY `fk_funcionario` (`criadopor_id`),
  ADD KEY `fk_produto_empresa` (`idEmpresa`);

--
-- Índices para tabela `produtoslotes`
--
ALTER TABLE `produtoslotes`
  ADD PRIMARY KEY (`idlote`),
  ADD KEY `idproduto` (`idproduto`),
  ADD KEY `fk_lote_empresa` (`idEmpresa`),
  ADD KEY `fk_criado_por` (`criadopor_id`);

--
-- Índices para tabela `saida`
--
ALTER TABLE `saida`
  ADD PRIMARY KEY (`id_saida`),
  ADD KEY `fk_criadopor` (`criadopor_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alertas_ocultos`
--
ALTER TABLE `alertas_ocultos`
  MODIFY `id_oculto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `cadastros`
--
ALTER TABLE `cadastros`
  MODIFY `idCadastro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `configuracoes_alertas`
--
ALTER TABLE `configuracoes_alertas`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `idEmpresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `loja_virtual`
--
ALTER TABLE `loja_virtual`
  MODIFY `idItem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de tabela `participantes_loja`
--
ALTER TABLE `participantes_loja`
  MODIFY `idParticipacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `idProduto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `produtoslotes`
--
ALTER TABLE `produtoslotes`
  MODIFY `idlote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `saida`
--
ALTER TABLE `saida`
  MODIFY `id_saida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `cadastros`
--
ALTER TABLE `cadastros`
  ADD CONSTRAINT `fk_usuario_empresa` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`idEmpresa`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `configuracoes_gerais`
--
ALTER TABLE `configuracoes_gerais`
  ADD CONSTRAINT `configuracoes_gerais_ibfk_1` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`idEmpresa`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `empresa`
--
ALTER TABLE `empresa`
  ADD CONSTRAINT `fk_adm` FOREIGN KEY (`idAdm`) REFERENCES `cadastros` (`idCadastro`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limitadores para a tabela `participantes_loja`
--
ALTER TABLE `participantes_loja`
  ADD CONSTRAINT `participantes_loja_ibfk_1` FOREIGN KEY (`idItem`) REFERENCES `loja_virtual` (`idItem`),
  ADD CONSTRAINT `participantes_loja_ibfk_2` FOREIGN KEY (`id_primeiroParticipante`) REFERENCES `cadastros` (`idCadastro`),
  ADD CONSTRAINT `participantes_loja_ibfk_3` FOREIGN KEY (`id_segundoParticipante`) REFERENCES `cadastros` (`idCadastro`);

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_funcionario` FOREIGN KEY (`criadopor_id`) REFERENCES `cadastros` (`idCadastro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produto_empresa` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`idEmpresa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `produtoslotes`
--
ALTER TABLE `produtoslotes`
  ADD CONSTRAINT `fk_criado_por` FOREIGN KEY (`criadopor_id`) REFERENCES `cadastros` (`idCadastro`),
  ADD CONSTRAINT `fk_lote_empresa` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`idEmpresa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produtoslotes_ibfk_1` FOREIGN KEY (`idproduto`) REFERENCES `produtos` (`idProduto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `saida`
--
ALTER TABLE `saida`
  ADD CONSTRAINT `fk_criadopor` FOREIGN KEY (`criadopor_id`) REFERENCES `cadastros` (`idCadastro`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
