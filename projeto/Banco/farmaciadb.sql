-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30/11/2025 às 04:06
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
-- Banco de dados: `farmaciadb`
--

DELIMITER $$

CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_registrar_venda_completa` (IN `p_id_cliente` INT, IN `p_total` DECIMAL(10,2), IN `p_itens_json` JSON)   BEGIN
  DECLARE v_id_venda INT DEFAULT NULL;
  DECLARE v_len INT DEFAULT 0;
  DECLARE v_i INT DEFAULT 0;
  DECLARE v_auto_baixa VARCHAR(10) DEFAULT '0';
  DECLARE v_item JSON;
  DECLARE v_id_med INT;
  DECLARE v_qtd INT;
  DECLARE v_preco DECIMAL(10,2);

  SELECT valor INTO v_auto_baixa FROM sistema_config WHERE chave = 'auto_baixa_estoque' LIMIT 1;
  IF v_auto_baixa IS NULL THEN
    SET v_auto_baixa = '0';
  END IF;

  START TRANSACTION;
    INSERT INTO vendas (`data`, id_cliente, total) 
    VALUES (NOW(), p_id_cliente, p_total);

    SET v_id_venda = LAST_INSERT_ID();
    SET v_len = JSON_LENGTH(p_itens_json);

    SET v_i = 0;
    WHILE v_i < v_len DO
      SET v_item = JSON_EXTRACT(p_itens_json, CONCAT('$[', v_i, ']'));

      SET v_id_med = JSON_UNQUOTE(JSON_EXTRACT(v_item, '$.id_medicamento'));
      SET v_qtd = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_item, '$.quantidade')) AS SIGNED);
      SET v_preco = CAST(JSON_UNQUOTE(JSON_EXTRACT(v_item, '$.preco_unitario')) AS DECIMAL(10,2));

      INSERT INTO itensvenda (id_venda, id_medicamento, quantidade, preco_unitario)
        VALUES (v_id_venda, v_id_med, v_qtd, v_preco);

      IF v_auto_baixa = '1' THEN
        UPDATE medicamento
        SET quantidade_estoque = GREATEST(0, quantidade_estoque - v_qtd)
        WHERE id_medicamento = v_id_med;
      END IF;

      SET v_i = v_i + 1;
    END WHILE;
  COMMIT;

  SELECT v_id_venda AS id_venda_criada;
END$$

--
-- Funções
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_total_gasto_cliente` (`p_id_cliente` INT) RETURNS DECIMAL(10,2) DETERMINISTIC READS SQL DATA BEGIN
  RETURN COALESCE((SELECT SUM(total) FROM vendas WHERE id_cliente = p_id_cliente), 0.00);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `nome`, `cpf`, `telefone`, `endereco`) VALUES
(2, 'Pedro Alvares', '173.894.625-09', '(21)98765-4321', 'Avenida Principal 456'),
(5, 'Mariana Ferreira', '491.750.238-62', '(51)96543-2109', 'Largo da Alegria 202'),
(10, 'Pedro Alvares', '614.987.502-31', '(21)98765-4321', 'Avenida Principal, 456'),
(13, 'Mariana Ferreira', '421.896.053-74', '(51)96543-2109', 'Largo da Alegria, 202'),
(50, 'Helena Costa', '100.579.246-80', '(11)93145-6789', 'Rua Alfa, 10'),
(51, 'Roberto Dias', '224.680.135-71', '(21)98642-0135', 'Avenida Beta, 20'),
(52, 'Valeria Rocha', '385.791.024-62', '(31)97531-9087', 'Travessa Gama, 30'),
(53, 'Guilherme Neves', '476.082.913-53', '(41)96420-8765', 'Rua Delta, 40'),
(54, 'Leticia Pires', '567.193.042-84', '(51)95319-7654', 'Largo Epsilon, 50'),
(60, 'Helena Costa', '101.579.246-81', '(11)93145-6789', 'Rua Alfa, 10'),
(61, 'Roberto Dias', '284.680.135-72', '(21)98642-0135', 'Avenida Beta, 20'),
(62, 'Valeria Rocha', '388.791.024-63', '(31)97531-9087', 'Travessa Gama, 30'),
(63, 'Guilherme Neves', '476.082.913-54', '(41)96420-8765', 'Rua Delta, 40'),
(64, 'Leticia Pires', '437.193.042-85', '(51)95319-7654', 'Largo Epsilon, 50'),
(75, 'Helena Costa', '103.579.246-81', '(11)93145-6789', 'Rua Alfa, 10'),
(76, 'Roberto Dias', '294.680.135-72', '(21)98642-0135', 'Avenida Beta, 20'),
(77, 'Valeria Rocha', '385.791.024-63', '(31)97531-9087', 'Travessa Gama, 30'),
(78, 'Guilherme Neves', '471.082.913-54', '(41)96420-8765', 'Rua Delta, 40'),
(79, 'Leticia Pires', '567.193.042-85', '(51)95319-7654', 'Largo Epsilon, 50'),
(80, 'João Pedro Fonseca', '86534354687', '19996541706', 'Rua das Araras, 123, Centro, Araras - SP'),
(81, 'JOAO PEDRO FONSECA', '544.658.768-76', '(19) 99654-1706', 'R bahia');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itensvenda`
--

CREATE TABLE `itensvenda` (
  `id_item` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `id_medicamento` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itensvenda`
--

INSERT INTO `itensvenda` (`id_item`, `id_venda`, `id_medicamento`, `quantidade`, `preco_unitario`) VALUES
(7, 50, 50, 2, 8.50),
(8, 51, 51, 2, 15.00),
(9, 52, 52, 1, 19.99),
(10, 53, 53, 1, 25.00),
(11, 54, 54, 1, 60.00),
(12, 54, 50, 1, 5.99);

-- --------------------------------------------------------

--
-- Estrutura para tabela `medicamento`
--

CREATE TABLE `medicamento` (
  `id_medicamento` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `quantidade_estoque` int(11) NOT NULL,
  `data_validade` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `medicamento`
--

INSERT INTO `medicamento` (`id_medicamento`, `nome`, `preco`, `quantidade_estoque`, `data_validade`, `ativo`) VALUES
(50, 'Paracetamol', 10.00, 150, '2029-06-05', 1),
(51, 'Captopril', 15.00, 70, '2030-03-15', 0),
(52, 'Losartana', 19.99, 80, '2026-11-17', 1),
(53, 'Metformina', 25.00, 60, '2027-12-03', 1),
(54, 'Sertralina', 60.00, 40, '2028-02-16', 1),
(55, 'Dipirona', 13.00, 55, '2027-07-23', 1),
(56, 'dipirona', 20.00, 30, '2027-07-26', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_auditoria_vendas`
--

CREATE TABLE `sistema_auditoria_vendas` (
  `id_auditoria` int(11) NOT NULL,
  `id_venda` int(11) DEFAULT NULL,
  `acao` varchar(30) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `data_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `valor_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_config`
--

CREATE TABLE `sistema_config` (
  `chave` varchar(100) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sistema_config`
--

INSERT INTO `sistema_config` (`chave`, `valor`) VALUES
('auto_baixa_estoque', '0');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sistema_meta`
--

CREATE TABLE `sistema_meta` (
  `meta_chave` varchar(100) NOT NULL,
  `meta_valor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sistema_meta`
--

INSERT INTO `sistema_meta` (`meta_chave`, `meta_valor`) VALUES
('versao_schema', '1.0');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id_venda` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id_venda`, `data`, `id_cliente`, `total`) VALUES
(50, '2025-10-16 10:00:00', 50, 17.00),
(51, '2025-10-16 11:30:00', 51, 30.00),
(52, '2025-10-16 14:45:00', 52, 19.99),
(53, '2025-10-16 16:00:00', 53, 25.00),
(54, '2025-10-16 17:30:00', 54, 65.99);

--
-- Acionadores `vendas`
--
DELIMITER $$
CREATE TRIGGER `trg_auditoria_vendas_ins` AFTER INSERT ON `vendas` FOR EACH ROW BEGIN
  INSERT INTO sistema_auditoria_vendas (id_venda, acao, descricao, valor_total)
  VALUES (
    NEW.id_venda,
    'INSERCAO_VENDA',
    CONCAT('Venda criada para o cliente ', NEW.id_cliente),
    NEW.total
  );
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `itensvenda`
--
ALTER TABLE `itensvenda`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_venda` (`id_venda`),
  ADD KEY `id_medicamento` (`id_medicamento`);

--
-- Índices de tabela `medicamento`
--
ALTER TABLE `medicamento`
  ADD PRIMARY KEY (`id_medicamento`);

--
-- Índices de tabela `sistema_auditoria_vendas`
--
ALTER TABLE `sistema_auditoria_vendas`
  ADD PRIMARY KEY (`id_auditoria`),
  ADD KEY `id_venda` (`id_venda`);

--
-- Índices de tabela `sistema_config`
--
ALTER TABLE `sistema_config`
  ADD PRIMARY KEY (`chave`);

--
-- Índices de tabela `sistema_meta`
--
ALTER TABLE `sistema_meta`
  ADD PRIMARY KEY (`meta_chave`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id_venda`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de tabela `itensvenda`
--
ALTER TABLE `itensvenda`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `medicamento`
--
ALTER TABLE `medicamento`
  MODIFY `id_medicamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de tabela `sistema_auditoria_vendas`
--
ALTER TABLE `sistema_auditoria_vendas`
  MODIFY `id_auditoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id_venda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itensvenda`
--
ALTER TABLE `itensvenda`
  ADD CONSTRAINT `itensvenda_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id_venda`),
  ADD CONSTRAINT `itensvenda_ibfk_2` FOREIGN KEY (`id_medicamento`) REFERENCES `medicamento` (`id_medicamento`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
