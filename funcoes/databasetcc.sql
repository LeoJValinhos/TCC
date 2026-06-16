-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 16-Jun-2026 às 07:53
-- Versão do servidor: 5.6.13
-- versão do PHP: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `databasetcc`
--
CREATE DATABASE IF NOT EXISTS `databasetcc` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `databasetcc`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cadastros`
--

CREATE TABLE IF NOT EXISTS `cadastros` (
  `idCadastro` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) NOT NULL,
  `sobrenome` varchar(60) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `email` varchar(60) NOT NULL,
  `datanasc` date NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `celular` varchar(11) NOT NULL,
  `idEmpresa` int(11) DEFAULT NULL,
  `tipocadastro` enum('EMPRESA/ADM','FUNCIONARIO') NOT NULL,
  PRIMARY KEY (`idCadastro`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `celular` (`celular`),
  KEY `fk_usuario_empresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=3 ;

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

CREATE TABLE IF NOT EXISTS `empresa` (
  `idEmpresa` int(11) NOT NULL AUTO_INCREMENT,
  `nomeEmpresa` varchar(300) NOT NULL,
  `CNPJ` char(14) NOT NULL,
  `codigoEmpresa` varchar(7) NOT NULL,
  `idAdm` int(11) DEFAULT NULL,
  `nomeAdm` varchar(30) DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idEmpresa`),
  UNIQUE KEY `CNPJ` (`CNPJ`),
  UNIQUE KEY `codigoEmpresa` (`codigoEmpresa`),
  KEY `fk_adm` (`idAdm`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=3 ;

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

CREATE TABLE IF NOT EXISTS `loja_virtual` (
  `idItem` int(11) NOT NULL AUTO_INCREMENT,
  `nomeProduto` varchar(100) NOT NULL,
  `marcaProduto` varchar(255) NOT NULL,
  `descricaoProduto` varchar(255) DEFAULT NULL,
  `quantidade` varchar(11) NOT NULL,
  `imagemProduto` varchar(255) NOT NULL,
  `meta` int(11) DEFAULT '2',
  `quantidadeParticipantes` int(11) DEFAULT '0',
  `status` enum('Aberta','Aguardando outro participante','Concluida','Cancelada') DEFAULT 'Aberta',
  PRIMARY KEY (`idItem`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

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

CREATE TABLE IF NOT EXISTS `participantes_loja` (
  `idParticipacao` int(11) NOT NULL AUTO_INCREMENT,
  `idItem` int(11) NOT NULL,
  `id_primeiroParticipante` int(11) DEFAULT NULL,
  `id_segundoParticipante` int(11) DEFAULT NULL,
  PRIMARY KEY (`idParticipacao`),
  KEY `idItem` (`idItem`),
  KEY `id_primeiroParticipante` (`id_primeiroParticipante`),
  KEY `id_segundoParticipante` (`id_segundoParticipante`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

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

CREATE TABLE IF NOT EXISTS `produtos` (
  `idProduto` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`idProduto`),
  UNIQUE KEY `unique_produto_empresa` (`NomeProduto`,`MarcaProduto`,`idEmpresa`),
  KEY `fk_funcionario` (`criadopor_id`),
  KEY `fk_produto_empresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=7 ;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `NomeProduto`, `MarcaProduto`, `Descricao`, `idEmpresa`, `criadopor_nome`, `criadoem`, `criadopor_id`, `preco_padrao_compra`, `preco_padrao_venda`, `estoque_minimo`) VALUES
(1, 'sabÃ£o em pÃ³', 'Brilho', '', 1, 'Leonardo', '2026-06-08 20:28:52', 1, '0.00', '0.00', 0),
(2, 'detergente', 'ype', '', 1, 'Leonardo', '2026-06-08 20:29:00', 1, '0.00', '0.00', 0),
(3, 'bolacha', 'trakinas', '', 1, 'Leonardo', '2026-06-08 20:29:06', 1, '0.00', '0.00', 0),
(4, 'Amaciante', 'Confort', 'limpa coisas', 1, 'Leonardo', '2026-06-16 00:58:20', 1, '25.00', '30.00', 50),
(5, 'Suco de Uva Integral 300ml', 'Aurora', 'Garrafa de vidro com suco de uva 100% integral, sem adiÃ§Ã£o de aÃ§Ãºcares ou conservantes.', 1, 'Leonardo', '2026-06-16 04:39:04', 1, '4.50', '7.50', 15),
(6, 'Salgadinho Assado de Queijo 60g', 'Fandangos', 'Salgadinho de milho assado sabor queijo, pacote de 60 gramas.', 1, 'Leonardo', '2026-06-16 04:39:41', 1, '2.20', '4.50', 20);

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtoslotes`
--

CREATE TABLE IF NOT EXISTS `produtoslotes` (
  `idlote` int(11) NOT NULL AUTO_INCREMENT,
  `idproduto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `validade` date DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `idEmpresa` int(11) DEFAULT NULL,
  `numero_lote` varchar(50) NOT NULL,
  `preco_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `preco_venda` decimal(10,2) NOT NULL DEFAULT '0.00',
  `desconto` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status_lote` enum('normal','promocao','vencido') NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`idlote`),
  KEY `idproduto` (`idproduto`),
  KEY `fk_lote_empresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `produtoslotes`
--

INSERT INTO `produtoslotes` (`idlote`, `idproduto`, `quantidade`, `validade`, `criado_em`, `idEmpresa`, `numero_lote`, `preco_compra`, `preco_venda`, `desconto`, `status_lote`) VALUES
(1, 3, 200, '2028-07-09', '2026-06-08 20:29:20', 1, '', '0.00', '0.00', '0.00', 'normal'),
(2, 4, 120, '2026-07-21', '2026-06-16 01:10:00', 1, '3', '3000.00', '3600.00', '0.00', ''),
(3, 1, 149, '2026-06-15', '2026-06-16 01:19:49', 1, '4', '3000.00', '3500.00', '0.00', 'vencido');

--
-- Constraints for dumped tables
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
  ADD CONSTRAINT `fk_lote_empresa` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`idEmpresa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produtoslotes_ibfk_1` FOREIGN KEY (`idproduto`) REFERENCES `produtos` (`idProduto`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
