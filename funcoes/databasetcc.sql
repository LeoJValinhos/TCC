-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 27-Jun-2026 às 00:50
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
(2, 'vinicius', 'sales', '012345678', 'vini@vini', '2008-07-20', '01234567812', '88888888888', 2, 'EMPRESA/ADM');

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
(1, 'Leleco Interpraise', '33333333333333', '8554687', 1, 'Leonardo', '2026-06-08 20:25:10'),
(2, 'vini corp', '01829742386474', '1622009', 2, 'vinicius', '2026-06-11 21:44:31');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `loja_virtual`
--

INSERT INTO `loja_virtual` (`idItem`, `nomeProduto`, `marcaProduto`, `descricaoProduto`, `quantidade`, `imagemProduto`, `meta`, `quantidadeParticipantes`, `status`) VALUES
(5, 'Sabao em po', 'Brilhante', 'Sabao em po para limpeza', '200', '../../imagens/sabao_brilho.png', 2, 2, 'Concluida'),
(6, 'Televisão 49 polegadas', 'Sansung', 'TV ULTRA 8K WIDE MUITO TOP 49 E TANTAS POLEGADAS', '850', '../../imagens/tvlg.png', 2, 1, 'Aguardando outro participante');

-- --------------------------------------------------------

--
-- Estrutura da tabela `participantes_loja`
--

CREATE TABLE `participantes_loja` (
  `idParticipacao` int(11) NOT NULL,
  `idItem` int(11) NOT NULL,
  `id_primeiroParticipante` int(11) DEFAULT NULL,
  `id_segundoParticipante` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `participantes_loja`
--

INSERT INTO `participantes_loja` (`idParticipacao`, `idItem`, `id_primeiroParticipante`, `id_segundoParticipante`) VALUES
(1, 5, 1, 2),
(2, 6, 1, NULL);

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
  `estoque_minimo` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `NomeProduto`, `MarcaProduto`, `Descricao`, `idEmpresa`, `criadopor_nome`, `criadoem`, `criadopor_id`, `preco_padrao_compra`, `preco_padrao_venda`, `estoque_minimo`) VALUES
(1, 'sabÃ£o em pÃ³', 'Brilho', '', 1, 'Leonardo', '2026-06-08 20:28:52', 1, '0.00', '0.00', 0),
(2, 'detergente', 'ype', '', 1, 'Leonardo', '2026-06-08 20:29:00', 1, '0.00', '0.00', 0),
(3, 'bolacha', 'trakinas', '', 1, 'Leonardo', '2026-06-08 20:29:06', 1, '0.00', '0.00', 0),
(4, 'Amaciante', 'Confort', 'limpa coisas', 1, 'Leonardo', '2026-06-16 00:58:20', 1, '25.00', '30.00', 50),
(5, 'Suco de Uva Integral 300ml', 'Aurora', 'Garrafa de vidro com suco de uva 100% integral, sem adiÃ§Ã£o de aÃ§Ãºcares ou conservantes.', 1, 'Leonardo', '2026-06-16 04:39:04', 1, '4.50', '7.50', 15),
(6, 'Salgadinho Assado de Queijo 60g', 'Fandangos', 'Salgadinho de milho assado sabor queijo, pacote de 60 gramas.', 1, 'Leonardo', '2026-06-16 04:39:41', 1, '2.20', '4.50', 20),
(7, 'Arroz Integral Agulhinha 5kg', 'Tio JoÃ£o', 'Arroz integral tipo 1, rico em fibras e minerais. Pacote de 5kg com grÃ£os selecionados.', 1, 'Leonardo', '2026-06-25 04:37:59', 1, '18.50', '29.90', 13),
(8, 'CafÃ© Tradicional VÃ¡cuo 500g', 'PilÃ£o', 'CafÃ© torrado e moÃ­do tradicional, com ponto de torra acentuado e sabor forte e marcante.', 1, 'Leonardo', '2026-06-25 04:38:39', 1, '11.20', '18.50', 20),
(9, 'Azeite de Oliva Extra Virgem 500ml', 'Gallo', 'Azeite de oliva extra virgem de acidez mÃ¡xima 0,5%. Garrafa de vidro de 500ml.', 1, 'Leonardo', '2026-06-25 04:39:22', 1, '22.00', '36.90', 8),
(10, 'Leite Integral UHT 1L', 'ItambÃ©', 'Leite caixinha UHT integral homogeneizado. Caixa com 1 litro.', 1, 'Leonardo', '2026-06-25 04:40:04', 1, '3.07', '5.49', 40);

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
  `quantidade_saida` int(11) NOT NULL,
  `motivo_saida` varchar(255) NOT NULL,
  `data_saida` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `saida`
--

INSERT INTO `saida` (`id_saida`, `idlote`, `id_lote`, `quantidade_saida`, `motivo_saida`, `data_saida`) VALUES
(1, 2, 2, 2, 'Venda', '2026-06-25 00:33:55'),
(2, 2, 2, 2, 'Ajuste', '2026-06-25 00:39:26'),
(3, 7, 7, 1, 'Ajuste', '2026-06-25 04:53:22'),
(4, 4, 4, 1, 'Vencimento', '2026-06-25 04:53:30'),
(5, 5, 5, 1, 'Avaria', '2026-06-25 04:53:36'),
(6, 7, 7, 1, 'Ajuste', '2026-06-25 04:53:41'),
(7, 2, 2, 12, 'Vencimento', '2026-06-25 04:53:50'),
(8, 5, 5, 7, 'Ajuste', '2026-06-25 04:54:01'),
(9, 4, 4, 1, 'Ajuste', '2026-06-25 06:04:59');

--
-- Índices para tabelas despejadas
--

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
  ADD PRIMARY KEY (`id_saida`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cadastros`
--
ALTER TABLE `cadastros`
  MODIFY `idCadastro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `idEmpresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `loja_virtual`
--
ALTER TABLE `loja_virtual`
  MODIFY `idItem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `participantes_loja`
--
ALTER TABLE `participantes_loja`
  MODIFY `idParticipacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_saida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
