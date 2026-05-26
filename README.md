# Atualizações recentes

Consulte o arquivo `atualizacao_14-05-2026.md` para detalhes das últimas alterações importantes realizadas no sistema (campo WhatsApp em devedores e importação de devedores).

# 🏪 Elite Sistema - Loja de Roupas e Camisetas de Time

Um sistema completo e profissional para gerenciamento de loja de roupas com suporte a código de barras, autenticação de usuários e painel administrativo intuitivo.

## ✨ Características Principais

- **🔐 Autenticação Segura** - Login/Registro com criptografia bcrypt
- **📦 Gerenciamento de Produtos** - CRUD completo com categorias
- **🔍 Leitor de Código de Barras** - Busca automática de produtos
- **📊 Dashboard Profissional** - Estatísticas e relatórios em tempo real
- **� Importação de Devedores** - CSV para cadastro rápido de fiados
- **�📈 Controle de Estoque** - Acompanhe quantidades disponíveis
- **💾 Banco de Dados** - MySQL com schema otimizado
- **🎨 Design Responsivo** - Interface moderna e intuitiva

## 🛠️ Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- XAMPP ou similar
- Navegador moderno

## 📁 Estrutura do Projeto

```
elite_sistema/
├── index.php                    # Página inicial
├── login.php                    # Login de usuário
├── registro.php                 # Cadastro de novo usuário
├── dashboard.php                # Painel administrativo
├── produtos.php                 # Listagem de produtos
├── novo-produto.php             # Formulário novo produto
├── editar-produto.php           # Formulário editar produto
├── painel-leitor.php            # Painel de leitura de código
├── importar-devedores.php       # Importação de devedores via CSV
├── logout.php                   # Sair do sistema
│
├── api/
│   ├── auth.php                 # API de autenticação
│   ├── produtos.php             # API de gerenciamento de produtos
│   └── importar_devedores.php   # API de importação de devedores
│
├── banco/
│   ├── config.php               # Configuração e conexão com BD
│   └── schema.sql               # Schema SQL do banco de dados
│
├── css/
│   └── style.css                # Estilos CSS responsivos
│
├── js/
│   └── app.js                   # JavaScript da aplicação
│
└── README.md                    # Este arquivo
```

## 🚀 Instalação

### 1. Preparar o Banco de Dados

```bash
# Abra o phpMyAdmin
# Crie um novo banco de dados chamado: elite_sistema
# Importe o arquivo banco/schema.sql
```

**Ou use SQL diretamente:**

```sql
-- No MySQL/phpMyAdmin
CREATE DATABASE elite_sistema;
USE elite_sistema;
-- Execute o conteúdo do arquivo banco/schema.sql
```

### 2. Configurar Conexão com Banco

Abra `banco/config.php` e configure:

```php
$host = 'localhost';
$usuario_bd = 'root';
$senha_bd = '';  // Deixe vazio se não tem senha
$banco = 'elite_sistema';
```

### 3. Acessar a Aplicação

```
http://localhost/elite_sistema/
```

## 🔑 Credenciais de Teste

**Email:** admin@elite.com  
**Senha:** 123456

## 📖 Como Usar

### Login
1. Acesse a página inicial
2. Clique em "Fazer Login"
3. Use as credenciais demo ou crie sua conta

### Cadastrar Novo Produto
1. No Dashboard, clique em "Novo Produto" ou vá para "Produtos" → "Adicionar"
2. Preencha os campos obrigatórios:
   - Código de Barras (único)
   - Nome do Produto
   - Categoria
   - Preço
3. Clique em "Salvar Produto"

### Usar Leitor de Código de Barras
1. Vá para "Leitor de Código"
2. Digite ou escaneie o código de barras
3. O sistema busca e exibe automaticamente:
   - Nome do produto
   - Preço
   - Estoque disponível
   - Tamanho e cor

### Gerenciar Produtos
1. Vá para "Produtos"
2. Visualize todos os produtos cadastrados
3. Use "Editar" para modificar ou "Deletar" para remover

## 🗂️ Tabelas do Banco de Dados

### usuarios
- id (INT, PK)
- nome (VARCHAR)
- email (VARCHAR, UNIQUE)
- senha (VARCHAR, bcrypt hash)
- telefone (VARCHAR)
- data_criacao (TIMESTAMP)
- status (ENUM: ativo/inativo)

### categorias
- id (INT, PK)
- nome (VARCHAR, UNIQUE)
- descricao (TEXT)
- data_criacao (TIMESTAMP)

### produtos
- id (INT, PK)
- codigo_barras (VARCHAR, UNIQUE)
- nome (VARCHAR)
- descricao (TEXT)
- categoria_id (INT, FK)
- preco (DECIMAL)
- estoque (INT)
- tamanho (VARCHAR)
- cor (VARCHAR)
- marca (VARCHAR)
- imagem (VARCHAR)
- data_criacao (TIMESTAMP)
- data_atualizacao (TIMESTAMP)
- status (ENUM: ativo/inativo)

### vendas
- id (INT, PK)
- usuario_id (INT, FK)
- total (DECIMAL)
- metodo_pagamento (VARCHAR)
- data_venda (TIMESTAMP)
- observacoes (TEXT)

### vendas_itens
- id (INT, PK)
- venda_id (INT, FK)
- produto_id (INT, FK)
- quantidade (INT)
- preco_unitario (DECIMAL)
- subtotal (DECIMAL)

### leitura_barras_log
- id (INT, PK)
- usuario_id (INT, FK)
- codigo_barras (VARCHAR)
- produto_id (INT, FK)
- data_leitura (TIMESTAMP)
- sucesso (BOOLEAN)

## 🎨 Personalização

### Alterar Cores Primárias
Edite `css/style.css`:

```css
:root {
    --cor-primaria: #1e40af;      /* Azul principal */
    --cor-secundaria: #0f766e;    /* Verde secundário */
    --cor-sucesso: #22c55e;       /* Verde de sucesso */
    --cor-aviso: #f59e0b;         /* Laranja de aviso */
    --cor-erro: #ef4444;          /* Vermelho de erro */
}
```

### Adicionar Novas Categorias
Execute no MySQL:

```sql
INSERT INTO categorias (nome, descricao) VALUES
('Nova Categoria', 'Descrição da categoria');
```

## 🔒 Segurança

- ✅ Senhas criptografadas com bcrypt
- ✅ Validação de entrada em frontend e backend
- ✅ Proteção contra SQL Injection
- ✅ Verificação de sessão em todas as páginas
- ✅ Escape de strings no banco de dados

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique se o banco de dados foi criado corretamente
2. Confirme as credenciais de conexão em `banco/config.php`
3. Verifique se o PHP está ativado no XAMPP
4. Limpe o cache do navegador

## 📝 Licença

Este projeto é fornecido como está para uso pessoal e comercial.

## ✍️ Autor messias moreira de sa

Desenvolvido com ❤️ para gerenciamento eficiente de lojas de roupas.
para qualquer tipo de sistema 

---

**Versão:** 2.8.9  
**Data:** 2026  
**Status:** Ativo
