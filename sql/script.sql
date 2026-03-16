-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 12-Mar-2026 às 21:44
-- Versão do servidor: 10.5.28-MariaDB-ubu2004
-- versão do PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dados: `php_crud`
--
CREATE DATABASE IF NOT EXISTS `php_crud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `php_crud`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
CREATE TABLE IF NOT EXISTS `utilizadores` (
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user` varchar(50) NOT NULL,
  `email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `AK_email` (`email`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncar tabela antes do insert `utilizadores`
--

TRUNCATE TABLE `utilizadores`;
--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`username`, `password`, `user`, `email`) VALUES
('admin', 'admin', 'Administrador', 'admin@escola.pt'),
('aluno', '$2y$12$Y3k7RUedBNLYhiwFL3LmuO6AkJoRdqNO6DiLeaE6ah32e2Wqdb3SO', 'aluno de exemplo', 'aluno@escola.pt'),
('prof', 'prof', 'Professor', 'professor@escola.pt'),
('teste', '$2y$12$/A3Z4ar.n4F6E1duUcimQ.9GwI9L23pX0945Cii2.cOp/zxgLucvC', 'utilizador de teste', 'teste@teste.pt');
COMMIT;

DROP TABLE IF EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
	 code			    CHAR(3) 	    NOT NULL 
	,designation	VARCHAR(20)	  NOT NULL 
	,level			  int DEFAULT 1	NULL
	,PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `profile`;

INSERT INTO `profile` (`code`, `designation`, `level`) 
  VALUES  ('ADM', 'Administrador', 1),
          ('PRO', 'Professor', 2),
          ('ALN', 'Aluno', 2),
		      ('ADN','administrativo',2);

ALTER TABLE utilizadores ADD 
	 COLUMN profile 	CHAR(3) DEFAULT 'ADM' NOT NULL;

ALTER TABLE utilizadores ADD
	CONSTRAINT fk_user_profile FOREIGN KEY (profile)
		REFERENCES profile (code);          

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
