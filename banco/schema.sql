-- Elite Sistema - Banco de Dados para Loja de Roupas e Camisetas de Time
-- =====================================================================

CREATE DATABASE IF NOT EXISTS elite_sistema;
USE elite_sistema;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS fiados;
DROP TABLE IF EXISTS leitura_barras_log;
DROP TABLE IF EXISTS vendas_itens;
DROP TABLE IF EXISTS vendas;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;
SET FOREIGN_KEY_CHECKS = 1;

-- Tabela de Usuários (Administradores)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo'
);

-- Usuário inicial para testes
INSERT IGNORE INTO usuarios (nome, email, senha) VALUES (
    'Administrador',
    'admin@elite.com',
    '$2y$10$Bno9r3vO29ARSGUif5Q/c.LESl5MRFu46yaJJuA34BuqHpiGWBFVq'
);

-- Tabela de Produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_barras VARCHAR(50) UNIQUE NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    usuario_id INT DEFAULT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    estoque INT DEFAULT 0,
    tamanho VARCHAR(10),
    cor VARCHAR(50),
    marca VARCHAR(100),
    imagem VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('ativo','inativo') DEFAULT 'ativo',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Produto de exemplo para teste
INSERT IGNORE INTO produtos (codigo_barras, nome, descricao, usuario_id, preco, estoque, tamanho, cor, marca) VALUES (
    '7891234567890',
    'Camisa de Time Exemplo',
    'Produto teste cadastrado automaticamente para validação do sistema.',
    (SELECT id FROM usuarios WHERE email = 'admin@elite.com' LIMIT 1),
    159.90,
    10,
    'M',
    'Vermelho',
    'Elite'
);

-- Consulta de exemplo para verificar produto no banco:
-- SELECT codigo_barras AS codigo, nome, preco AS valor FROM produtos;

-- Tabela de Vendas
CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(12, 2) NOT NULL,
    metodo_pagamento VARCHAR(50),
    data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observacoes TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de Itens de Venda
CREATE TABLE IF NOT EXISTS vendas_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Tabela de Leitura de Códigos (Log)
CREATE TABLE IF NOT EXISTS leitura_barras_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    codigo_barras VARCHAR(50),
    produto_id INT,
    data_leitura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sucesso BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);


-- Tabela de Fiados
DROP TABLE IF EXISTS fiados;
CREATE TABLE fiados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    telefone VARCHAR(30),
    valor DECIMAL(12,2) NOT NULL,
    data_vencimento DATE DEFAULT NULL,
    descricao TEXT,
    interagiu_whatsapp BOOLEAN DEFAULT FALSE,
    status ENUM('pendente','pago') DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO fiados (nome, telefone, valor, data_vencimento, descricao, interagiu_whatsapp, status) VALUES
('João Silva', '(11) 99999-0000', 150.00, '2026-05-10', 'Venda no crediário', FALSE, 'pendente'),
('Maria Souza', '(21) 98888-1111', 320.50, '2026-05-15', 'Pagamento parcelado', TRUE, 'pendente'),
('Carlos Pereira', '(31) 97777-2222', 0.00, '2026-04-20', 'Dívida quitada', FALSE, 'pago');

-- Índices para melhor performance
CREATE INDEX idx_codigo_barras ON produtos(codigo_barras);
CREATE INDEX idx_usuario_email ON usuarios(email);
CREATE INDEX idx_venda_usuario ON vendas(usuario_id);