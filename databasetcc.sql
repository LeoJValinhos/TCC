-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/01/2026 às 20:58
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

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
-- Estrutura para tabela `empresa`
--

CREATE TABLE `empresa` (
  `IDEmpresa` int(11) NOT NULL,
  `NomeEmpresa` varchar(80) NOT NULL,
  `CodEMPRESA` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `IDFuncionario` int(11) NOT NULL,
  `NomeFuncionario` varchar(80) NOT NULL,
  `TipoCadastro` enum('adm','funcionario') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `idProduto` int(11) NOT NULL,
  `NomeProduto` varchar(100) NOT NULL,
  `MarcaProduto` varchar(100) NOT NULL,
  `Descricao` varchar(255) DEFAULT NULL,
  `Criadopor` int(11) NOT NULL,
  `criadoem` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtoslotes`
--

CREATE TABLE `produtoslotes` (
  `idlote` int(11) NOT NULL,
  `idproduto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `validade` date DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`IDEmpresa`),
  ADD UNIQUE KEY `CodEMPRESA` (`CodEMPRESA`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`IDFuncionario`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`idProduto`),
  ADD UNIQUE KEY `NomeProduto` (`NomeProduto`),
  ADD KEY `Criadopor` (`Criadopor`);

--
-- Índices de tabela `produtoslotes`
--
ALTER TABLE `produtoslotes`
  ADD PRIMARY KEY (`idlote`),
  ADD KEY `idproduto` (`idproduto`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `IDEmpresa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `IDFuncionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `idProduto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtoslotes`
--
ALTER TABLE `produtoslotes`
  MODIFY `idlote` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`Criadopor`) REFERENCES `funcionarios` (`IDFuncionario`);

--
-- Restrições para tabelas `produtoslotes`
--
ALTER TABLE `produtoslotes`
  ADD CONSTRAINT `produtoslotes_ibfk_1` FOREIGN KEY (`idproduto`) REFERENCES `produtos` (`idProduto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
