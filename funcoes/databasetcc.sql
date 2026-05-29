-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 29-Maio-2026 às 01:05
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
  `tipocadastro` enum('EMPRESA/ADM','INDEPENDENTE/USUARIO') NOT NULL,
  PRIMARY KEY (`idCadastro`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `celular` (`celular`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `cadastros`
--

INSERT INTO `cadastros` (`idCadastro`, `nome`, `sobrenome`, `senha`, `email`, `datanasc`, `cpf`, `celular`, `tipocadastro`) VALUES
(1, 'VINI', 'MAMACO DA SILVA', 'vini4675', 'root@root', '2007-03-20', '77777777777', '88888888888', 'EMPRESA/ADM'),
(2, 'leleco', 'valinhos', '87654321', 'leleco@leleco', '2026-03-11', '88888888888', '11111111111', 'EMPRESA/ADM'),
(3, 'marcelo', 'silva', '11111111', 'marcelo@gmail.com', '2026-03-11', '99999999999', '99999999999', 'EMPRESA/ADM'),
(4, 'joshua', 'gabriel', '12345678', 'teste@1', '2007-01-24', '36529545215', '85486541485', 'EMPRESA/ADM');

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
--

CREATE TABLE IF NOT EXISTS `empresa` (
  `idEmpresa` int(11) NOT NULL AUTO_INCREMENT,
  `nomeEmpresa` varchar(300) NOT NULL,
  `CNPJ` char(14) NOT NULL,
  `idAdm` int(11) DEFAULT NULL,
  `nomeAdm` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idEmpresa`),
  KEY `fk_adm` (`idAdm`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE IF NOT EXISTS `produtos` (
  `idProduto` int(11) NOT NULL AUTO_INCREMENT,
  `NomeProduto` varchar(100) NOT NULL,
  `MarcaProduto` varchar(100) NOT NULL,
  `Descricao` varchar(255) DEFAULT NULL,
  `criadopor_nome` varchar(100) DEFAULT NULL,
  `criadoem` datetime DEFAULT CURRENT_TIMESTAMP,
  `criadopor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  UNIQUE KEY `unique_produto_marca` (`MarcaProduto`),
  KEY `fk_funcionario` (`criadopor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`idProduto`, `NomeProduto`, `MarcaProduto`, `Descricao`, `criadopor_nome`, `criadoem`, `criadopor_id`) VALUES
(1, 'Refrigerante', 'Fanta', '', 'VINI', '2026-05-25 20:06:01', 1),
(2, 'detergente', 'veja', '', 'VINI', '2026-05-25 20:07:26', 1),
(3, 'detergente', 'ype', '', 'VINI', '2026-05-25 20:07:35', 1);

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
  PRIMARY KEY (`idlote`),
  KEY `idproduto` (`idproduto`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `produtoslotes`
--

INSERT INTO `produtoslotes` (`idlote`, `idproduto`, `quantidade`, `validade`, `criado_em`) VALUES
(1, 1, 200, '2008-02-28', '2026-05-25 20:06:35');

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `empresa`
--
ALTER TABLE `empresa`
  ADD CONSTRAINT `fk_adm` FOREIGN KEY (`idAdm`) REFERENCES `cadastros` (`idCadastro`);

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_funcionario` FOREIGN KEY (`criadopor_id`) REFERENCES `cadastros` (`idCadastro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `produtoslotes`
--
ALTER TABLE `produtoslotes`
  ADD CONSTRAINT `produtoslotes_ibfk_1` FOREIGN KEY (`idproduto`) REFERENCES `produtos` (`idProduto`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
