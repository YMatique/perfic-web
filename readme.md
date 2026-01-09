# 💰 Perfic - Sistema de Controle Financeiro Pessoal

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Livewire-3-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3">
  <img src="https://img.shields.io/badge/TailwindCSS-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
</div>

## 📖 Sobre o Projeto

**Perfic** é um sistema completo de controle financeiro pessoal desenvolvido com Laravel 11 e Livewire 3. O sistema oferece gestão inteligente de finanças com recursos avançados de análise, metas automáticas, transações recorrentes e interface moderna e responsiva.

## ✨ Funcionalidades Implementadas

### 🏠 Dashboard Inteligente

-   **Visão Geral Financeira**: Cards com estatísticas em tempo real
-   **Interface Responsiva**: Otimizada para desktop e mobile
-   **Modo Escuro/Claro**: Alternância automática baseada na preferência
-   **Navegação Intuitiva**: Sidebar colapsível com ícones Material

### 💳 Gestão de Transações Completa

-   **CRUD Completo**: Criar, editar, visualizar e excluir transações
-   **Filtros Avançados**: Por tipo, categoria, período e busca textual
-   **Categorização**: Sistema de categorias coloridas com ícones
-   **Histórico Detalhado**: Lista com paginação e ordenação
-   **Validações Robustas**: Prevenção de dados inválidos

### 🏷️ Sistema de Categorias Inteligente

-   **17 Categorias Padrão**: Pré-configuradas para uso imediato
-   **Categorias Personalizadas**: Crie e organize suas próprias categorias
-   **Ícones Material**: Interface visual consistente
-   **Cores Personalizáveis**: Identifique rapidamente cada categoria
-   **Separação por Tipo**: Receitas e despesas organizadas

### 🎯 Metas Financeiras Avançadas

-   **4 Tipos de Metas**:
    -   **Limite de Gastos**: Controle gastos totais
    -   **Meta de Poupança**: Objetivos de economia
    -   **Limite por Categoria**: Controle específico
    -   **Meta de Receita**: Objetivos de renda
-   **Cálculo Automático**: Progresso baseado em transações reais
-   **Períodos Flexíveis**: Diário, semanal, mensal, trimestral, anual
-   **Alertas Visuais**: Status colorido (ativa, atenção, concluída)
-   **Filtros Inteligentes**: Por tipo, status e período

### 🔄 Transações Recorrentes Automáticas

-   **6 Frequências Disponíveis**:
    -   Diário, Semanal, Mensal, Bimestral, Trimestral, Anual
-   **Configuração Flexível**: Data início/fim, dia específico
-   **Execução Automática**: Via comando cron
-   **Execução Manual**: Botão para executar imediatamente
-   **Histórico Completo**: Rastreamento de todas as execuções
-   **Gestão de Status**: Ativar/desativar facilmente

### ⚙️ Configurações e Personalização

-   **Perfil do Usuário**: Gestão completa de dados pessoais
-   **Configurações de Aparência**: Modo escuro/claro
-   **Configurações de Senha**: Alteração segura
-   **Multi-tenancy**: Isolamento total de dados por usuário

### 🍞 Sistema de Notificações

-   **4 Tipos de Toast**: Sucesso, erro, aviso, informação
-   **Animações Suaves**: Transições elegantes
-   **Auto-dismiss**: Fechamento automático
-   **Design Responsivo**: Funciona em todas as telas
-   **Suporte Dark Mode**: Cores adaptáveis

## 🛠️ Tecnologias Utilizadas

### Backend Robusto

-   **Laravel 11**: Framework PHP moderno e robusto
-   **PHP 8.2+**: Linguagem de programação
-   **MySQL/PostgreSQL**: Banco de dados relacional
-   **Eloquent ORM**: Mapeamento objeto-relacional avançado

### Frontend Moderno

-   **Livewire 3**: Framework full-stack para Laravel
-   **Alpine.js**: Framework JavaScript reativo
-   **TailwindCSS**: Framework CSS utility-first
-   **Material Icons**: Biblioteca de ícones do Google

### Funcionalidades Avançadas

-   **Multi-tenancy**: Isolamento de dados por usuário
-   **UUID**: Identificadores únicos universais
-   **Soft Deletes**: Exclusão lógica de registros
-   **Global Scopes**: Filtros automáticos por tenant
-   **Command Pattern**: Comandos artisan personalizados

## 🚀 Instalação e Configuração

### Pré-requisitos

-   PHP 8.2 ou superior
-   Composer
-   Node.js e NPM
-   MySQL ou PostgreSQL
-   Git

### Instalação Rápida

1. **Clone o repositório**

```bash
git clone https://github.com/seu-usuario/perfic.git
cd perfic
```

2. **Instale as dependências**

```bash
composer install
npm install
```

3. **Configure o ambiente**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
   Edite o arquivo `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perfic
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. **Execute as migrações**

```bash
php artisan migrate
```

6. **Setup automático com dados de exemplo**

```bash
# Setup completo (recomendado)
php artisan perfic:setup

# Ou apenas categorias padrão
php artisan perfic:setup --categories
```

7. **Compile os assets**

```bash
npm run build
# ou para desenvolvimento
npm run dev
```

8. **Inicie o servidor**

```bash
php artisan serve
```

O sistema estará disponível em `http://localhost:8000`

## 📁 Estrutura do Projeto

```
perfic/
├── app/
│   ├── Console/Commands/       # Comandos Artisan personalizados
│   │   ├── ExecuteRecurringTransactions.php
│   │   └── PerficSetupCommand.php
│   ├── Livewire/              # Componentes Livewire
│   │   ├── CategoryManager.php
│   │   ├── GoalManager.php
│   │   ├── RecurringTransactionManager.php
│   │   ├── TransactionManager.php
│   │   └── Settings/
│   ├── Models/                # Modelos Eloquent
│   │   ├── Tenant.php
│   │   ├── Transaction.php
│   │   ├── Category.php
│   │   ├── Goal.php
│   │   ├── RecurringTransaction.php
│   │   ├── AiInsight.php
│   │   └── ...
│   └── Traits/                # Traits reutilizáveis
│       └── WithToast.php
├── database/
│   ├── migrations/            # Migrações do banco
│   └── seeders/               # Seeders com dados iniciais
│       ├── PerficSeeder.php
│       ├── DefaultCategoriesSeeder.php
│       └── DemoDataSeeder.php
├── resources/
│   ├── views/
│   │   ├── livewire/          # Views dos componentes
│   │   └── components/        # Componentes Blade
│   ├── css/
│   └── js/
└── routes/
    ├── web.php                # Rotas web
    └── auth.php               # Rotas de autenticação
```

## 🎨 Interface de Usuário

### Design System

-   **Cores**: Paleta moderna com suporte completo a modo escuro
-   **Tipografia**: Font Inter para melhor legibilidade
-   **Iconografia**: Material Icons para consistência visual
-   **Componentes**: Sistema de design baseado em TailwindCSS

### Responsividade Completa

-   **Mobile First**: Otimizado para dispositivos móveis
-   **Breakpoints**: Adaptações para sm, md, lg, xl
-   **Touch Friendly**: Interface otimizada para toque
-   **Sidebar Colapsível**: Aproveita melhor o espaço

## 🔒 Segurança

-   **Autenticação Completa**: Sistema baseado em Laravel Breeze
-   **Multi-tenancy**: Isolamento total de dados entre usuários
-   **Validação Robusta**: Validação em todos os formulários
-   **CSRF Protection**: Proteção contra ataques CSRF
-   **SQL Injection**: Proteção via Eloquent ORM
-   **XSS Protection**: Escape automático de conteúdo

## ⚡ Comandos Disponíveis

### Setup e Configuração

```bash
# Setup completo interativo
php artisan perfic:setup

# Apenas categorias padrão
php artisan perfic:setup --categories

# Apenas dados de demonstração
php artisan perfic:setup --demo

# Para usuário específico
php artisan perfic:setup --user=123

# Sem confirmações (automático)
php artisan perfic:setup --force
```

### Transações Recorrentes

```bash
# Visualizar o que seria executado
php artisan perfic:execute-recurring --dry-run

# Executar transações pendentes
php artisan perfic:execute-recurring

# Executar para usuário específico
php artisan perfic:execute-recurring --user=123
```

### Configurar Automação (Cron)

```bash
# Adicionar ao crontab para execução diária às 9h
0 9 * * * cd /caminho/do/projeto && php artisan perfic:execute-recurring
```

## 🎯 Status do Projeto

### ✅ Funcionalidades Implementadas

-   [x] **Sistema de autenticação** completo com Laravel Breeze
-   [x] **Gestão de transações** - CRUD completo com filtros avançados
-   [x] **Gestão de categorias** - 17 categorias padrão + personalizadas
-   [x] **Metas financeiras** - 4 tipos com cálculo automático
-   [x] **Transações recorrentes** - 6 frequências com automação
-   [x] **Sistema de notificações** - Toast completo com animações
-   [x] **Interface responsiva** - Layout moderno e funcional
-   [x] **Modo escuro/claro** - Alternância automática
-   [x] **Multi-tenancy** - Isolamento de dados por usuário
-   [x] **Seeders inteligentes** - Dados de exemplo realistas
-   [x] **Comandos Artisan** - Automação e setup

### 🚧 Próximas Implementações

-   [ ] **Dashboard dinâmico** com gráficos e estatísticas reais
-   [ ] **Sistema de relatórios** com gráficos interativos
-   [ ] **Insights de IA** com análise de padrões
-   [ ] **Exportação de dados** (PDF, Excel, CSV)
-   [ ] **API REST** completa para integração
-   [ ] **PWA** (Progressive Web App)

## 📊 Dados de Demonstração

O sistema inclui um conjunto completo de dados realistas:

### Categorias Padrão (17)

-   **Receitas**: Salário, Freelances, Investimentos, Outros
-   **Despesas**: Alimentação, Transporte, Moradia, Saúde, Educação, Compras, Entretenimento, Contas, Poupança, etc.

### Dados Demo Incluídos

-   **~200 transações** dos últimos 3 meses
-   **6 transações recorrentes** (salário, contas automáticas)
-   **4 metas financeiras** com progresso calculado
-   **Valores realistas** em MZN (Meticais moçambicanos)

### Usuário Demo

-   **Email**: demo@perfic.com
-   **Senha**: password

## 🤝 Contribuição

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Diretrizes de Contribuição

-   Siga os padrões de código PSR-12
-   Escreva testes para novas funcionalidades
-   Documente mudanças significativas
-   Use commits semânticos

## 📄 Licença

Este projeto está licenciado sob a MIT License - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 👥 Equipe

-   **Desenvolvedor Principal**: [Yuvi Matique](https://github.com/YMatique)

## 📞 Suporte

-   **Email**: yuvimatique@gmail.com
-   **Issues**: [GitHub Issues](https://github.com/YMatique/perfic/issues)
-   **Documentação**: [Wiki do Projeto](https://github.com/YMatique/perfic/wiki)

## 🙏 Agradecimentos

-   Laravel Team pelo framework incrível
-   Livewire Team pela simplicidade e poder
-   TailwindCSS pelo sistema de design
-   Comunidade open source

---

<div align="center">
  <p>Feito com ❤️ para ajudar você a controlar melhor suas finanças</p>
  
  ⭐ **Se este projeto te ajudou, não esqueça de dar uma estrela!** ⭐
</div>
