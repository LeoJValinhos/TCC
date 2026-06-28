-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 28-Jun-2026 às 11:40
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
(1, 'Leonardo', 'Valinhos', 'vini4675', 'root@root', '2007-03-20', '11111111111', '22222222222', 1, 'EMPRESA/ADM'),
(2, 'vinicius', 'sales', '012345678', 'vini@vini', '2008-07-20', '01234567812', '88888888888', 2, 'EMPRESA/ADM'),
(3, 'aa', 'sss', '12345678', 'leo@leleco', '2007-03-20', '22132132131', '13213212332', 1, 'EMPRESA/ADM'),
(4, 'Jorge', 'Harrison', '012345678', 'Jorge@gmail.com', '2007-03-16', '48798978779', '77987798789', 3, 'EMPRESA/ADM');

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
-- Estrutura da tabela `empresa`
--

CREATE TABLE `empresa` (
  `idEmpresa` int(11) NOT NULL,
  `nomeEmpresa` varchar(300) NOT NULL,
  `CNPJ` char(14) NOT NULL,
  `codigoEmpresa` varchar(7) NOT NULL,
  `idAdm` int(11) DEFAULT NULL,
  `nomeAdm` varchar(30) DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`idEmpresa`, `nomeEmpresa`, `CNPJ`, `codigoEmpresa`, `idAdm`, `nomeAdm`, `criado_em`) VALUES
(1, 'Leleco Interpraise', '33333333333333', '8554687', 3, 'aa', '2026-06-08 20:25:10'),
(2, 'vini corp', '01829742386474', '1622009', 2, 'vinicius', '2026-06-11 21:44:31'),
(3, 'JHLF', '78787897898789', '2424760', 4, 'Jorge', '2026-06-27 17:29:27');

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
  `status` enum('Aberta','Aguardando outro participante','Concluida','Cancelada') DEFAULT 'Aberta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Extraindo dados da tabela `loja_virtual`
--

INSERT INTO `loja_virtual` (`idItem`, `nomeProduto`, `marcaProduto`, `descricaoProduto`, `quantidade`, `imagemProduto`, `meta`, `quantidadeParticipantes`, `status`) VALUES
(55, 'Sabão em Pó', 'Brilhante', 'Sabão em pó para limpeza profunda', '200', '../../imagens/sabao_brilho.png', 2, 2, 'Concluida'),
(56, 'Amaciante de Roupas', 'Soft', 'Amaciante concentrado 1L', '150', '../../imagens/amaciante_soft.png', 2, 2, 'Concluida'),
(57, 'Detergente Líquido', 'Limpex', 'Detergente neutro para louças 500ml', '300', '../../imagens/detergente_limpex.png', 3, 0, 'Aberta'),
(58, 'Desinfetante Pinho', 'Força', 'Desinfetante uso geral 1L', '120', '../../imagens/desinfetante_forca.png', 2, 0, 'Aberta'),
(59, 'Água Sanitária', 'Pura', 'Água sanitária para desinfecção 1L', '180', '../../imagens/agua_sanitaria_pura.png', 2, 0, 'Aberta'),
(60, 'Esponja de Aço', 'Brilhus', 'Pacote com 4 unidades', '250', '../../imagens/esponja_brilhante.png', 4, 0, 'Aberta'),
(61, 'Limpador Multiuso', 'Uau', 'Limpador spray multiuso 500ml', '140', '../../imagens/limpador_acao.png', 2, 0, 'Aberta'),
(62, 'Saco de Lixo 50L', 'DoverRoll', 'Pacote com 10 unidades', '160', '../../imagens/saco_lixo_resiste.png', 2, 0, 'Aberta'),
(63, 'Arroz Integral', 'Grão de ouro', 'Pacote de arroz integral 1kg', '90', '../../imagens/arroz_grao_ouro.png', 2, 0, 'Aberta'),
(64, 'Feijão Carioca', 'Dona Dê', 'Feijão carioca tipo 1 1kg', '110', '../../imagens/feijao_dona_benta.png', 2, 0, 'Aberta'),
(65, 'Macarrão Espaguete', 'Amália', 'Macarrão sêmola espaguete 500g', '210', '../../imagens/macarrao_massa_boa.png', 3, 0, 'Aberta'),
(66, 'Óleo de Soja', 'Soya', 'Óleo de soja garrafa 900ml', '170', '../../imagens/oleo_leve.png', 2, 0, 'Aberta'),
(67, 'Açúcar Refinado', 'União', 'Açúcar refinado pacote 1kg', '130', '../../imagens/acucar_doce_vida.png', 2, 0, 'Aberta'),
(68, 'Sal Refinado', 'Cisne', 'Sal refinado de cozinha 1kg', '80', '../../imagens/sal_iodado.png', 4, 0, 'Aberta'),
(69, 'Café Torrado e Moído', 'Pilão', 'Café a vácuo tradicional 500g', '140', '../../imagens/cafe_aroma.png', 2, 0, 'Aberta'),
(70, 'Farinha de Trigo', 'Dona Benta', 'Farinha de trigo tipo 1 1kg', '100', '../../imagens/farinha_premium.png', 2, 0, 'Aberta'),
(71, 'Achocolatado em Pó', 'Nescau', 'Lata de achocolatado 400g', '125', '../../imagens/achocolatado_chocomax.png', 2, 0, 'Aberta'),
(72, 'Cereal Matinal', 'Sucrilhos', 'Cereal de milho tradicional 300g', '85', '../../imagens/cereal_nutricroc.png', 2, 0, 'Aberta'),
(73, 'Biscoito Recheado', 'Passatempo', 'Biscoito sabor chocolate 130g', '400', '../../imagens/biscoito_doce_mania.png', 5, 0, 'Aberta'),
(74, 'Biscoito Salgado', 'Marilan', 'Biscoito água e sal 350g', '190', '../../imagens/biscoito_crack.png', 3, 0, 'Aberta'),
(75, 'Geleia de Morango', 'Predilecta', 'Pote de geleia de morango 230g', '60', '../../imagens/geleia_fruta_pura.png', 2, 0, 'Aberta'),
(76, 'Torrada Tradicional', 'Schar', 'Pacote de torradas leves 150g', '95', '../../imagens/torrada_crocante.png', 2, 0, 'Aberta'),
(77, 'Leite em Pó', 'Itambé', 'Leite em pó integral sachê 400g', '115', '../../imagens/leite_nutrivida.png', 2, 0, 'Aberta'),
(78, 'Aveia em Flocos', 'Yoki', 'Caixa de aveia em flocos finos 170g', '75', '../../imagens/aveia_natural.png', 3, 0, 'Aberta');

-- --------------------------------------------------------

--
-- Estrutura da tabela `participantes_loja`
--

CREATE TABLE `participantes_loja` (
  `idParticipacao` int(11) NOT NULL,
  `idItem` int(11) NOT NULL,
  `id_primeiroParticipante` int(11) DEFAULT NULL,
  `id_segundoParticipante` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `participantes_loja`
--

INSERT INTO `participantes_loja` (`idParticipacao`, `idItem`, `id_primeiroParticipante`, `id_segundoParticipante`) VALUES
(1, 5, 1, 2),
(2, 6, 1, NULL),
(8, 55, 3, 2),
(10, 56, 3, 4);

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
(1, 'sabÃ£o em pÃ³', 'Brilho', '', 1, 'Leonardo', '2026-06-08 20:28:52', 1, '0.00', '0.00', 0, NULL),
(2, 'detergente', 'ype', '', 1, 'Leonardo', '2026-06-08 20:29:00', 1, '0.00', '0.00', 0, NULL),
(3, 'bolacha', 'trakinas', '', 1, 'Leonardo', '2026-06-08 20:29:06', 1, '0.00', '0.00', 0, NULL),
(4, 'Amaciante', 'Confort', 'limpa coisas', 1, 'Leonardo', '2026-06-16 00:58:20', 1, '25.00', '30.00', 50, NULL),
(5, 'Suco de Uva Integral 300ml', 'Aurora', 'Garrafa de vidro com suco de uva 100% integral, sem adiÃ§Ã£o de aÃ§Ãºcares ou conservantes.', 1, 'Leonardo', '2026-06-16 04:39:04', 1, '4.50', '7.50', 15, NULL),
(6, 'Salgadinho Assado de Queijo 60g', 'Fandangos', 'Salgadinho de milho assado sabor queijo, pacote de 60 gramas.', 1, 'Leonardo', '2026-06-16 04:39:41', 1, '2.20', '4.50', 20, NULL),
(7, 'Arroz Integral Agulhinha 5kg', 'Tio JoÃ£o', 'Arroz integral tipo 1, rico em fibras e minerais. Pacote de 5kg com grÃ£os selecionados.', 1, 'Leonardo', '2026-06-25 04:37:59', 1, '18.50', '29.90', 13, NULL),
(8, 'CafÃ© Tradicional VÃ¡cuo 500g', 'PilÃ£o', 'CafÃ© torrado e moÃ­do tradicional, com ponto de torra acentuado e sabor forte e marcante.', 1, 'Leonardo', '2026-06-25 04:38:39', 1, '11.20', '18.50', 20, NULL),
(9, 'Azeite de Oliva Extra Virgem 500ml', 'Gallo', 'Azeite de oliva extra virgem de acidez mÃ¡xima 0,5%. Garrafa de vidro de 500ml.', 1, 'Leonardo', '2026-06-25 04:39:22', 1, '22.00', '36.90', 8, NULL),
(10, 'Leite Integral UHT 1L', 'ItambÃ©', 'Leite caixinha UHT integral homogeneizado. Caixa com 1 litro.', 1, 'Leonardo', '2026-06-25 04:40:04', 1, '3.07', '5.49', 40, NULL);

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
(1, 3, 200, '2028-07-09', '2026-06-08 20:29:20', 0, NULL, 1, '', '0.00', '0.00', '10.00', 'promocao'),
(2, 4, 97, '2026-07-21', '2026-06-16 01:10:00', 0, NULL, 1, '3', '3000.00', '3600.00', '20.00', 'promocao'),
(3, 1, 147, '2026-06-15', '2026-06-16 01:19:49', 0, NULL, 1, '4', '3000.00', '3500.00', '15.00', 'vencido'),
(4, 7, 48, '2030-02-12', '2026-06-25 04:43:01', 0, NULL, 1, '7_2030-02-12', '18.50', '29.89', '0.00', 'normal'),
(5, 9, 17, '2027-12-10', '2026-06-25 04:44:38', 0, NULL, 1, '9_2027-12-10', '22.00', '36.90', '0.00', 'normal'),
(6, 8, 80, '2027-04-15', '2026-06-25 04:45:31', 0, NULL, 1, '8_2027-04-15', '11.20', '18.50', '0.00', 'normal'),
(7, 10, 118, '2026-11-18', '2026-06-25 04:46:28', 0, NULL, 1, '10_2026-11-18', '3.10', '5.49', '0.00', 'normal'),
(8, 6, 100, '2028-05-05', '2026-06-25 04:47:08', 0, NULL, 1, '6_2028-05-05', '1.40', '2.65', '0.00', 'normal'),
(9, 5, 200, '2030-12-12', '2026-06-25 04:47:58', 0, NULL, 1, '5_2030-12-12', '5.40', '7.20', '0.00', 'normal'),
(10, 4, 987897, '2007-03-20', '2026-06-26 21:12:20', 1, 'Leonardo', 1, 'LOTE-4', '10.00', '20.00', '0.00', 'vencido');

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
(1, 2, 2, 0, '', 2, 'Venda', '2026-06-25 00:33:55'),
(2, 2, 2, 0, '', 2, 'Ajuste', '2026-06-25 00:39:26'),
(3, 7, 7, 0, '', 1, 'Ajuste', '2026-06-25 04:53:22'),
(4, 4, 4, 0, '', 1, 'Vencimento', '2026-06-25 04:53:30'),
(5, 5, 5, 0, '', 1, 'Avaria', '2026-06-25 04:53:36'),
(6, 7, 7, 0, '', 1, 'Ajuste', '2026-06-25 04:53:41'),
(7, 2, 2, 0, '', 12, 'Vencimento', '2026-06-25 04:53:50'),
(8, 5, 5, 0, '', 7, 'Ajuste', '2026-06-25 04:54:01'),
(9, 4, 4, 0, '', 1, 'Ajuste', '2026-06-25 06:04:59'),
(43, 2, 2, 3, 'aa', 1, 'Venda', '2026-06-27 15:58:30');

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
  MODIFY `id_oculto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cadastros`
--
ALTER TABLE `cadastros`
  MODIFY `idCadastro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `configuracoes_alertas`
--
ALTER TABLE `configuracoes_alertas`
  MODIFY `id_config` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `idEmpresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `loja_virtual`
--
ALTER TABLE `loja_virtual`
  MODIFY `idItem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de tabela `participantes_loja`
--
ALTER TABLE `participantes_loja`
  MODIFY `idParticipacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `idProduto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `produtoslotes`
--
ALTER TABLE `produtoslotes`
  MODIFY `idlote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `saida`
--
ALTER TABLE `saida`
  MODIFY `id_saida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `cadastros`
--
ALTER TABLE `cadastros`
  ADD CONSTRAINT `fk_usuario_empresa` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`idEmpresa`) ON DELETE SET NULL ON UPDATE CASCADE;

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
