# 📊 Sistema de Ocorrências do Ponto

Sistema web para gestão e análise de ocorrências de ponto de funcionários, desenvolvido com **Laravel 12** e **PostgreSQL** (via Docker). Permite importar planilhas Excel, consultar registros com filtros avançados e visualizar rankings por tipo de ocorrência.

---

## 📋 Índice

- [Funcionalidades](#-funcionalidades)
- [Screenshots](#-screenshots)
- [Tecnologias](#-tecnologias)
- [Arquitetura](#-arquitetura)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação](#-instalação)
- [Uso](#-uso)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Banco de Dados](#-banco-de-dados)
- [Perfis de Acesso](#-perfis-de-acesso)
- [Formato da Planilha](#-formato-da-planilha)

---

## ✨ Funcionalidades

### 📤 Upload de Planilha
- Importação de arquivos Excel (`.xls`, `.xlsx`) com validação automática de cabeçalho
- Processamento em lote com tratamento de células mescladas
- Normalização dos dados em duas tabelas relacionais (`funcionarios` e `ocorrencias`)
- Logs de importação com registro de sucesso/erro por arquivo

### 🔍 Consulta de Dados
- Filtros avançados: nome do funcionário, centro de custo, filial, tipo de ocorrência, período (data início/fim) e duração
- Autocomplete dinâmico nos campos de busca com debounce
- Paginação de resultados
- Exportação para Excel (`.xlsx`) com limite de 10.000 registros

### 📈 Indicadores / Rankings
- Visualização dos tipos de ocorrência em carrossel com contagem total
- Ranking decrescente de funcionários por tipo de ocorrência selecionado
- Barras de progresso animadas proporcionais à quantidade
- Paginação com 10 funcionários por página
- Destaque visual para o pódio (Top 3)

### 👥 Gestão de Usuários
- CRUD completo de usuários (criar, editar, excluir)
- Dois perfis de acesso: **Admin** e **Visualizador**
- Proteção contra exclusão do próprio usuário

### 📝 Logs de Importação
- Histórico de todos os uploads realizados
- Registro de: arquivo, usuário, linhas processadas, erros e data/hora

---

## 🛠 Tecnologias

| Camada       | Tecnologia                                                              |
| ------------ | ----------------------------------------------------------------------- |
| **Backend**  | PHP 8.2+, Laravel 12                                                    |
| **Frontend** | Blade Templates, Tailwind CSS (CDN), JavaScript Vanilla                 |
| **Banco**    | PostgreSQL (via Docker)                                                 |
| **Planilha** | PhpSpreadsheet 5.x (leitura/escrita de Excel)                          |
| **Design**   | Glassmorphism, Dark Theme, Inter (Google Fonts), Micro-animações        |

---

## 🏗 Arquitetura

O projeto segue a arquitetura **MVC** do Laravel com uma camada adicional de **Services** para separar regras de negócio:

```
Request → Controller → Service → Model → Database
                 ↓
              View (Blade + Tailwind)
```

### Padrões utilizados
- **Service Layer**: `PlanilhaService` e `ConsultaService` encapsulam a lógica de processamento e consulta
- **Middleware customizado**: `CheckRole` para controle de acesso baseado em perfil
- **AJAX + JSON API**: Endpoints separados para ranking e autocomplete (respostas assíncronas)

---

## 📦 Pré-requisitos

- **PHP** >= 8.2 com as extensões:
  - `dom`, `xml`, `mbstring`, `sqlite3`, `pdo_sqlite`, `gd`, `intl`, `curl`, `zip`
- **Composer** >= 2.x
- **Node.js** >= 18.x e **NPM** >= 9.x
- **Git**

---

## 🚀 Instalação

### 1. Clonar o repositório


### 2. Instalar dependências PHP

```bash
composer install
```

### 3. Instalar dependências Node

```bash
npm install
```

### 4. Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Subir o banco de dados (Docker) e rodar migrations

```bash
docker compose up -d
# Caso dê erro de permissão no Docker, use: sudo docker compose up -d

php artisan migrate
```

### 6. Criar o primeiro usuário admin

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@empresa.com',
    'password' => bcrypt('suaSenhaSegura'),
    'role' => 'admin',
]);
```

### 7. Iniciar o servidor de desenvolvimento

```bash
composer dev
```

Isso inicia simultaneamente: servidor PHP, fila de jobs, logs em tempo real e Vite.

Ou, para iniciar apenas o servidor:

```bash
php artisan serve
```

Acesse: **http://localhost:8000**

---

## 💻 Uso

### Fluxo típico

1. **Login** → Acesse com suas credenciais
2. **Upload** *(Admin)* → Faça upload da planilha de ocorrências no formato esperado
3. **Consulta** → Utilize os filtros para encontrar ocorrências específicas
4. **Indicadores** → Selecione um tipo de ocorrência para visualizar o ranking de funcionários
5. **Exportar** → Exporte os resultados filtrados para Excel

---

## 📁 Estrutura do Projeto

```
sistema-ocorrencias/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php        # Login e logout
│   │   │   ├── ConsultaController.php    # Consulta com filtros e exportação
│   │   │   ├── IndicadorController.php   # Rankings e indicadores (AJAX)
│   │   │   ├── LogController.php         # Histórico de importações
│   │   │   ├── UploadController.php      # Upload de planilhas
│   │   │   └── UserController.php        # CRUD de usuários
│   │   └── Middleware/
│   │       └── CheckRole.php             # Controle de acesso por perfil
│   ├── Models/
│   │   ├── Funcionario.php               # Model com relação hasMany → Ocorrencia
│   │   ├── Ocorrencia.php                # Model com relação belongsTo → Funcionario
│   │   └── User.php                      # Model de autenticação
│   └── Services/
│       ├── ConsultaService.php           # Filtros, contagem e geração de Excel
│       └── PlanilhaService.php           # Parser da planilha Excel (stateful)
├── database/
│   ├── database_schema.sql               # Script DDL do banco PostgreSQL
│   └── migrations/                       # Migrações do schema
├── resources/
│   └── views/
│       ├── consulta.blade.php            # Tela de consulta com filtros
│       ├── indicadores.blade.php         # Tela de indicadores/rankings
│       ├── login.blade.php               # Tela de login
│       ├── upload.blade.php              # Tela de upload de planilha
│       ├── users/                        # Views de gestão de usuários
│       └── logs/                         # Views de logs de importação
├── routes/
│   └── web.php                           # Todas as rotas da aplicação
├── composer.json                         # Dependências PHP
└── package.json                          # Dependências Node.js
```

---

## 🗄 Banco de Dados

### Diagrama de Relacionamento

```
┌──────────────────┐       ┌──────────────────────┐
│   funcionarios   │       │     ocorrencias      │
├──────────────────┤       ├──────────────────────┤
│ id (PK)          │──┐    │ id (PK)              │
│ coligada         │  │    │ funcionario_id (FK)   │──┐
│ filial           │  └───▶│ tipo_ocorrencia       │  │
│ nome             │       │ inicio_origem         │  │
│ horario          │       │ fim_origem            │  │
│ cc_desc          │       │ dt_referencia         │  │
│ cc               │       │ duracao_hhmm          │  │
│ situacao         │       │ atitude               │  │
│ timestamps       │       │ modificador_por       │  │
└──────────────────┘       │ total_geral           │  │
                           │ timestamps            │  │
                           └──────────────────────┘  │
                                                      │
                     1 Funcionário ◄── N Ocorrências ──┘
```

### Tabelas auxiliares
- **`users`** — Usuários do sistema (autenticação + perfil)
- **`logs_importacao`** — Histórico de uploads (arquivo, usuário, sucesso/erro)
- **`sessions`**, **`cache`**, **`jobs`** — Tabelas de infraestrutura do Laravel

---

## 🔐 Perfis de Acesso

| Funcionalidade         | Admin | Visualizador |
| ---------------------- | :---: | :----------: |
| Consultar ocorrências  |  ✅   |      ✅      |
| Exportar para Excel    |  ✅   |      ✅      |
| Indicadores / Rankings |  ✅   |      ✅      |
| Upload de planilha     |  ✅   |      ❌      |
| Gestão de usuários     |  ✅   |      ❌      |
| Visualizar logs        |  ✅   |      ❌      |

---

## 📄 Formato da Planilha

A planilha Excel importada deve seguir a estrutura abaixo. O sistema valida o cabeçalho na **linha 3**:

| Coluna | Campo            | Descrição                              |
| :----: | ---------------- | -------------------------------------- |
|   A    | Coligada         | Código da coligada                     |
|   C    | Filial           | Código da filial                       |
|   I    | Funcionário      | Nome completo do funcionário           |
|   M    | Horario          | Horário de trabalho                    |
|   N    | CC_DESC          | Descrição do centro de custo           |
|   O    | CC               | Código do centro de custo              |
|   P    | Situação         | Situação do funcionário                |
|   Q    | Ocorrência       | Tipo da ocorrência (ex: Atraso, Falta) |
|   R    | Inicio Origem    | Data/hora de início                    |
|   S    | Fim Origem       | Data/hora de fim                       |
|   T    | Dt Referência    | Data de referência                     |
|   U    | DURACAO_HHMM     | Duração no formato HH:MM              |
|   V    | ATITUDE          | Atitude registrada                     |
|   W    | Modificador Por  | Usuário que modificou o registro       |
|   X    | Total Geral      | Total geral acumulado                  |

> **Nota:** Os dados iniciam na **linha 4**. Células mescladas são tratadas automaticamente pelo parser.

---

## 📜 Licença

Este projeto é de uso interno e proprietário.

---

<p align="center">
  Desenvolvido com ❤️ utilizando <strong>Laravel 12</strong>
</p>
# sistema_ocorrencias_v2
