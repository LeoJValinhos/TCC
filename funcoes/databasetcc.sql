-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 11-Jun-2026 às 01:05
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  `idEmpresa` int(11) NOT NULL,
  `criadopor_nome` varchar(100) DEFAULT NULL,
  `criadoem` datetime DEFAULT CURRENT_TIMESTAMP,
  `criadopor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`idProduto`),
  UNIQUE KEY `unique_produto_empresa` (`NomeProduto`,`MarcaProduto`,`idEmpresa`),
  KEY `fk_funcionario` (`criadopor_id`),
  KEY `fk_produto_empresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`idlote`),
  KEY `idproduto` (`idproduto`),
  KEY `fk_lote_empresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

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
