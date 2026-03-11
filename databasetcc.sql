-- phpMyAdmin SQL Dump CORRIGIDO
-- Banco: databasetcc

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `databasetcc`;
USE `databasetcc`;

-- --------------------------------------------------------
-- TABELA: cadastros
-- --------------------------------------------------------

CREATE TABLE `cadastros` (
  `idCadastro` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(30) NOT NULL,
  `sobrenome` VARCHAR(60) NOT NULL,
  `senha` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(60) NOT NULL UNIQUE,
  `datanasc` DATE NOT NULL,
  `cpf` VARCHAR(11) NOT NULL UNIQUE,
  `celular` VARCHAR(11) NOT NULL UNIQUE,
  `tipocadastro` ENUM('EMPRESA/ADM','INDEPENDENTE/USUARIO') NOT NULL,
  PRIMARY KEY (`idCadastro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- TABELA: funcionarios (CRIADA PARA RESOLVER O ERRO)
-- --------------------------------------------------------

CREATE TABLE `funcionarios` (
  `IDFuncionario` INT(11) NOT NULL AUTO_INCREMENT,
  `nomeFuncionario` VARCHAR(100) NOT NULL,
  `emailFuncionario` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`IDFuncionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- TABELA: produtos
-- --------------------------------------------------------

CREATE TABLE `produtos` (
  `idProduto` INT(11) NOT NULL AUTO_INCREMENT,
  `NomeProduto` VARCHAR(100) NOT NULL,
  `MarcaProduto` VARCHAR(100) NOT NULL,
  `Descricao` VARCHAR(255) DEFAULT NULL,
  `Criadopor` INT(11) NOT NULL,
  `criadoem` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idProduto`),
  UNIQUE KEY `NomeProduto` (`NomeProduto`),
  KEY `Criadopor` (`Criadopor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- TABELA: produtoslotes
-- --------------------------------------------------------

CREATE TABLE `produtoslotes` (
  `idlote` INT(11) NOT NULL AUTO_INCREMENT,
  `idproduto` INT(11) NOT NULL,
  `quantidade` INT(11) NOT NULL,
  `validade` DATE DEFAULT NULL,
  `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idlote`),
  KEY `idproduto` (`idproduto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- FOREIGN KEYS
-- --------------------------------------------------------

ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1`
  FOREIGN KEY (`Criadopor`)
  REFERENCES `funcionarios` (`IDFuncionario`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `produtoslotes`
  ADD CONSTRAINT `produtoslotes_ibfk_1`
  FOREIGN KEY (`idproduto`)
  REFERENCES `produtos` (`idProduto`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;