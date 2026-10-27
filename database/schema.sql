-- =========================================================
-- FindWare - Sistema Inteligente de Localização de Produtos
-- Script de criação do banco de dados
-- =========================================================
--
-- OBS: o dump original do projeto estava desatualizado em relação
-- ao código (faltavam as colunas `nivel` em `produtos` e
-- `codigo_barras`/`tipo` em `movimentacoes`). Este script já vem
-- corrigido e pronto para uso com a versão MVC do sistema.

CREATE DATABASE IF NOT EXISTS cd_system CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE cd_system;

-- ---------------------------------------------------------
-- Tabela: usuarios
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    senha VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- Tabela: produtos
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS produtos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) DEFAULT NULL,
    codigo VARCHAR(50) DEFAULT NULL,
    codigo_barras VARCHAR(100) DEFAULT NULL,
    corredor VARCHAR(10) DEFAULT NULL,
    prateleira VARCHAR(10) DEFAULT NULL,
    nivel VARCHAR(10) DEFAULT NULL,
    estoque INT(11) DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- Tabela: movimentacoes
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    codigo_barras VARCHAR(100) DEFAULT NULL,
    produto VARCHAR(100) DEFAULT NULL,
    quantidade INT(11) DEFAULT NULL,
    localizacao VARCHAR(100) DEFAULT NULL,
    tipo VARCHAR(20) DEFAULT NULL,
    data_movimentacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------
-- Dados de exemplo (opcional, útil para a apresentação)
-- Usuário de teste -> e-mail: admin@findware.com | senha: 123456
-- (senha em texto simples só para agilizar o teste; o cadastro pela
--  tela de "Criar novo usuário" já salva a senha com hash seguro)
-- ---------------------------------------------------------

INSERT INTO usuarios (nome, email, senha) VALUES
('Administrador', 'admin@findware.com', '123456');

INSERT INTO produtos (nome, codigo, codigo_barras, corredor, prateleira, nivel, estoque) VALUES
('Mouse Gamer', '789123', '789123', 'A', '3', '2', 24),
('Teclado Mecânico', '456789', '456789', 'B', '2', '1', 50),
('Monitor 24"', '112233', '112233', 'C', '1', '3', 4);

INSERT INTO movimentacoes (codigo_barras, produto, quantidade, localizacao, tipo, data_movimentacao) VALUES
('789123', 'Mouse Gamer', 24, 'A-3-N2', 'ENTRADA', NOW()),
('456789', 'Teclado Mecânico', 50, 'B-2-N1', 'ENTRADA', NOW());
