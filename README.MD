# 💰 Perfic - Sistema de Controle Financeiro Pessoal

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Livewire-3-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3">
  <img src="https://img.shields.io/badge/TailwindCSS-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
</div>

## 📖 Sobre o Projeto

**Perfic** é um sistema completo de controle financeiro pessoal desenvolvido com Laravel 11 e Livewire 3. O sistema permite aos usuários gerenciar suas finanças de forma inteligente, com recursos avançados de análise, insights baseados em IA e interface moderna e responsiva.

## ✨ Funcionalidades Principais

### 🏠 Dashboard Inteligente
- **Visão Geral Financeira**: Cards com saldo total, receitas, despesas e economia do mês
- **Gráficos Interativos**: Visualização de tendências e padrões de gastos
- **Insights por IA**: Análises automáticas e sugestões personalizadas
- **Transações Recentes**: Lista das últimas movimentações financeiras

### 💳 Gestão de Transações
- **CRUD Completo**: Criar, editar, visualizar e excluir transações
- **Categorização Automática**: IA classifica transações baseada em padrões
- **Filtros Avançados**: Por tipo, categoria, data e localização
- **Busca Inteligente**: Pesquisa por descrição e localização
- **Suporte a Anexos**: Upload de comprovantes e documentos
- **Geolocalização**: Registro automático do local da transação

### 🏷️ Sistema de Categorias
- **Categorias Personalizáveis**: Crie e organize suas próprias categorias
- **Ícones e Cores**: Interface visual com Material Icons
- **Categorias por Tipo**: Separação entre receitas e despesas
- **Ordenação Customizada**: Organize categorias por prioridade

### 🎯 Metas Financeiras
- **Metas de Gastos**: Definir limites por categoria e período
- **Metas de Economia**: Estabelecer objetivos de poupança
- **Acompanhamento Visual**: Progress bars e indicadores de status
- **Alertas Inteligentes**: Notificações quando próximo ao limite
- **Períodos Flexíveis**: Metas diárias, semanais, mensais, trimestrais e anuais

### 🤖 Inteligência Artificial
- **Categorização Automática**: Machine learning para classificar transações
- **Detecção de Anomalias**: Identifica gastos incomuns ou suspeitos
- **Insights Personalizados**: Análises e recomendações baseadas em comportamento
- **Previsões**: Projeções de gastos e tendências futuras
- **Padrões de Comportamento**: Análise de hábitos financeiros

### 📊 Relatórios e Analytics
- **Dashboard de KPIs**: Métricas financeiras importantes
- **Score Financeiro**: Pontuação baseada em comportamento
- **Relatórios Mensais**: Resumos automáticos por período
- **Análise de Tendências**: Gráficos de evolução temporal
- **Comparativos**: Análise período a período

### 🔄 Transações Recorrentes
- **Automatização**: Configure receitas e despesas recorrentes
- **Frequências Flexíveis**: Diária, semanal, mensal, bimestral, trimestral, anual
- **Gestão de Execução**: Controle automático de próximas execuções
- **Histórico Completo**: Rastreamento de todas as execuções

### ⚙️ Configurações e Personalização
- **Perfil do Usuário**: Gestão de dados pessoais
- **Temas**: Modo claro e escuro
- **Preferências**: Customização da interface
- **Backup Local**: Exportação e importação de dados
- **Configurações de IA**: Ajustes de confiança e regras

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 11**: Framework PHP moderno e robusto
- **PHP 8.2+**: Linguagem de programação
- **MySQL/PostgreSQL**: Banco de dados relacional
- **Eloquent ORM**: Mapeamento objeto-relacional

### Frontend
- **Livewire 3**: Framework full-stack para Laravel
- **Alpine.js**: Framework JavaScript reativo
- **TailwindCSS**: Framework CSS utility-first
- **Material Icons**: Biblioteca de ícones do Google

### Funcionalidades Avançadas
- **Multi-tenancy**: Isolamento de dados por usuário
- **UUID**: Identificadores únicos universais
- **Soft Deletes**: Exclusão lógica de registros
- **Global Scopes**: Filtros automáticos por tenant
- **Observers**: Listeners para eventos de modelo

## 🚀 Instalação e Configuração

### Pré-requisitos
- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- MySQL ou PostgreSQL
- Git

### Passos de Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/perfic.git
cd perfic
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Instale as dependências JavaScript**
```bash
npm install
```

4. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados**
Edite o arquivo `.env` com suas credenciais:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perfic
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

6. **Execute as migrações**
```bash
php artisan migrate
```

7. **Execute os seeders (opcional)**
```bash
php artisan db:seed
```

8. **Compile os assets**
```bash
npm run build
# ou para desenvolvimento
npm run dev
```

9. **Inicie o servidor**
```bash
php artisan serve
```

O sistema estará disponível em `http://localhost:8000`

## 📁 Estrutura do Projeto

```
perfic/
├── app/
│   ├── Livewire/              # Componentes Livewire
│   │   ├── CategoryManager.php
│   │   ├── TransactionManager.php
│   │   └── Settings/
│   ├── Models/                # Modelos Eloquent
│   │   ├── Tenant.php
│   │   ├── Transaction.php
│   │   ├── Category.php
│   │   ├── Goal.php
│   │   ├── AiInsight.php
│   │   └── ...
│   └── Traits/                # Traits reutilizáveis
├── database/
│   ├── migrations/            # Migrações do banco
│   └── seeders/               # Dados iniciais
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
- **Cores**: Paleta moderna com suporte a modo escuro
- **Tipografia**: Font Inter para melhor legibilidade
- **Iconografia**: Material Icons para consistência
- **Componentes**: Sistema de design baseado em TailwindCSS

### Responsividade
- **Mobile First**: Otimizado para dispositivos móveis
- **Breakpoints**: sm, md, lg, xl adaptações
- **Touch Friendly**: Interface otimizada para toque
- **PWA Ready**: Preparado para Progressive Web App

## 🔒 Segurança

- **Autenticação**: Sistema completo com Laravel Breeze
- **Autorização**: Políticas e gates para controle de acesso
- **Validação**: Validação robusta em todos os formulários
- **CSRF Protection**: Proteção contra ataques CSRF
- **SQL Injection**: Proteção via Eloquent ORM
- **XSS Protection**: Escape automático de conteúdo

## 🎯 Status do Projeto

### ✅ Funcionalidades Implementadas
- [x] Sistema de autenticação completo
- [x] Gestão de transações (CRUD)
- [x] Gestão de categorias (CRUD)
- [x] Dashboard com estatísticas
- [x] Sistema de configurações
- [x] Interface responsiva
- [x] Modo escuro/claro
- [x] Modelos de dados completos

### 🚧 Em Desenvolvimento
- [ ] Dashboard dinâmico com dados reais
- [ ] Sistema de metas financeiras
- [ ] Relatórios e gráficos
- [ ] Integração com IA
- [ ] Transações recorrentes
- [ ] Sistema de backup

### 📋 Roadmap Futuro
- [ ] API REST completa
- [ ] Aplicativo móvel
- [ ] Integração bancária (Open Banking)
- [ ] Importação de extratos
- [ ] Sistema de orçamentos
- [ ] Alertas e notificações
- [ ] Compartilhamento de relatórios

## 🤝 Contribuição

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Diretrizes de Contribuição
- Siga os padrões de código PSR-12
- Escreva testes para novas funcionalidades
- Documente mudanças significativas
- Use commits semânticos

## 📄 Licença

Este projeto está licenciado sob a MIT License - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 👥 Equipe

- **Desenvolvedor Principal**: [Seu Nome](https://github.com/seu-usuario)

## 📞 Suporte

- **Email**: suporte@perfic.com
- **Issues**: [GitHub Issues](https://github.com/seu-usuario/perfic/issues)
- **Documentação**: [Wiki do Projeto](https://github.com/seu-usuario/perfic/wiki)

## 🙏 Agradecimentos

- Laravel Team pelo framework incrível
- Livewire Team pela simplicidade e poder
- TailwindCSS pelo sistema de design
- Comunidade open source

---

<div align="center">
  <p>Feito com ❤️ para ajudar você a controlar melhor suas finanças</p>
  
  ⭐ **Se este projeto te ajudou, não esqueça de dar uma estrela!** ⭐
</div>