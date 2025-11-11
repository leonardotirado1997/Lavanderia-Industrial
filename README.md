# Sistema de Automação - LuvaSul Lavanderia Industrial

Sistema web completo em PHP + MySQL para digitalizar o processo de recebimento, triagem, lavagem e expedição de materiais, substituindo registros manuais em papel por controle digital com QR Code para rastreabilidade.

## 🚀 Características

- ✅ Dashboard com contadores em tempo real
- ✅ Recebimento de pedidos com geração automática de QR Code
- ✅ Controle de lavagem com leitura de QR Code
- ✅ Expedição com comprovante de entrega
- ✅ Relatórios completos com filtros e exportação CSV
- ✅ Interface moderna e responsiva com Bootstrap 5
- ✅ Rastreabilidade completa dos pedidos

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior (ou MariaDB)
- Servidor web (Apache/Nginx) ou servidor PHP embutido
- Extensão MySQLi habilitada no PHP
- Extensão GD habilitada no PHP (para processamento de imagens)

## 🔧 Instalação

### 1. Configurar Banco de Dados

Edite o arquivo `config/config.php` e ajuste as credenciais do banco de dados:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'luvasul_db');
```

### 2. Criar Banco de Dados

O sistema criará automaticamente o banco de dados e a tabela na primeira execução. Alternativamente, você pode executar o script SQL manualmente:

```bash
mysql -u root -p < database.sql
```

### 3. Permissões de Diretório

Certifique-se de que o diretório `qrcodes/` tenha permissão de escrita:

```bash
chmod 777 qrcodes/
```

### 4. Iniciar Servidor

#### Opção 1: Servidor PHP Embutido (Desenvolvimento)

```bash
php -S localhost:8000
```

Acesse: `http://localhost:8000`

#### Opção 2: Servidor Web (Produção)

Configure Apache/Nginx para apontar para o diretório do projeto.

## 📱 Uso do Sistema

### 1. Dashboard
- Visualize contadores de pedidos por status
- Acesse rapidamente as principais funcionalidades
- Veja os pedidos mais recentes

### 2. Recebimento
1. Preencha o formulário com os dados do pedido
2. Cliente, tipo de material e quantidade são obrigatórios
3. Ao salvar, um QR Code único é gerado automaticamente
4. Imprima o QR Code para rastreamento físico

### 3. Lavagem
1. Leia ou digite o código do QR Code (formato: PEDIDO-123)
2. O sistema atualiza o status para "Em Lavagem"
3. Visualize todas as informações do pedido

### 4. Expedição
1. Leia o QR Code do pedido que está pronto
2. O sistema atualiza o status para "Pronto para Expedição"
3. Ao expedir novamente, o pedido é marcado como "Concluído"
4. Um comprovante é gerado automaticamente

### 5. Relatórios
1. Filtre pedidos por status
2. Visualize todos os dados em tabela
3. Exporte para CSV com um clique

## 🔑 Geração de QR Codes

O sistema utiliza uma API online gratuita (QR Server) para gerar os QR Codes. Não é necessária instalação de bibliotecas adicionais.

Para usar uma biblioteca local (opcional):
1. Instale via Composer: `composer require endroid/qr-code`
2. O sistema detectará automaticamente e usará a biblioteca local

## 📁 Estrutura de Arquivos

```
LuvaSul/
├── config/
│   └── config.php          # Configurações do sistema
├── includes/
│   ├── header.php          # Cabeçalho e navbar
│   ├── footer.php          # Rodapé
│   └── qrcode_helper.php   # Funções para QR Code
├── pages/
│   ├── recebimento.php     # Página de recebimento
│   ├── lavagem.php         # Página de lavagem
│   ├── expedicao.php       # Página de expedição
│   └── relatorios.php      # Página de relatórios
├── assets/
│   ├── css/
│   │   └── style.css      # Estilos customizados
│   └── js/
│       └── main.js         # Scripts JavaScript
├── qrcodes/                # Diretório para QR Codes gerados
├── conexao.php             # Conexão com banco de dados
├── index.php               # Dashboard principal
├── database.sql            # Script SQL do banco
└── README.md               # Este arquivo
```

## 🎨 Personalização

### Cores e Tema
Edite o arquivo `assets/css/style.css` para personalizar cores, fontes e layout.

### Configurações
Ajuste as constantes em `config/config.php` para personalizar o nome do sistema e outras configurações.

## 🔒 Segurança

**Importante para Produção:**
- Altere as credenciais padrão do banco de dados
- Configure permissões adequadas nos diretórios
- Use HTTPS em produção
- Implemente autenticação de usuários se necessário
- Valide e sanitize todas as entradas do usuário (já implementado parcialmente)

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
- Verifique as credenciais em `config/config.php`
- Certifique-se de que o MySQL está rodando
- Verifique se o usuário tem permissão para criar bancos

### QR Code não aparece
- Verifique permissões do diretório `qrcodes/`
- Verifique conexão com internet (para API online)
- Verifique logs de erro do PHP

### Páginas não carregam corretamente
- Verifique se está usando o caminho correto do servidor
- Certifique-se de que todas as extensões PHP necessárias estão habilitadas

## 📝 Licença

Este projeto foi desenvolvido para uso interno da LuvaSul Lavanderia Industrial.

## 👨‍💻 Desenvolvimento

Sistema desenvolvido em PHP estruturado, sem frameworks, seguindo boas práticas de desenvolvimento web.

---

**Desenvolvido para LuvaSul Lavanderia Industrial** 🏭

