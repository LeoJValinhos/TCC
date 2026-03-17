-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 13, 2026 at 01:07 AM
-- Server version: 5.6.13
-- PHP Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `databasetcc`
--
CREATE DATABASE IF NOT EXISTS `databasetcc` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `databasetcc`;

-- --------------------------------------------------------

--
-- Table structure for table `cadastros`
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
  UNIQUE KEY `senha` (`senha`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `celular` (`celular`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `cadastros`
--

INSERT INTO `cadastros` (`idCadastro`, `nome`, `sobrenome`, `senha`, `email`, `datanasc`, `cpf`, `celular`, `tipocadastro`) VALUES
(1, 'VINI', 'MAMACO DA SILVA', 'vini4675', 'root@root', '2007-03-20', '77777777777', '88888888888', 'EMPRESA/ADM'),
(2, 'leleco', 'valinhos', '87654321', 'leleco@leleco', '2026-03-11', '88888888888', '11111111111', 'EMPRESA/ADM'),
(3, 'marcelo', 'silva', '11111111', 'marcelo@gmail.com', '2026-03-11', '99999999999', '99999999999', 'EMPRESA/ADM');

-- --------------------------------------------------------

--
-- Table structure for table `produtos`
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
  UNIQUE KEY `NomeProduto` (`NomeProduto`),
  KEY `fk_funcionario` (`criadopor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `produtoslotes`
--

CREATE TABLE IF NOT EXISTS `produtoslotes` (
  `idlote` int(11) NOT NULL AUTO_INCREMENT,
  `idproduto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `validade` date DEFAULT NULL,
  `criado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idlote`),
  KEY `idproduto` (`idproduto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_funcionario` FOREIGN KEY (`criadopor_id`) REFERENCES `cadastros` (`idCadastro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produtoslotes`
--
ALTER TABLE `produtoslotes`
  ADD CONSTRAINT `produtoslotes_ibfk_1` FOREIGN KEY (`idproduto`) REFERENCES `produtos` (`idProduto`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
