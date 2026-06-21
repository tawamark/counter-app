# Counter - Guia de Desenvolvimento

Este guia resume a primeira versão finalizada do projeto Counter e serve como referência rápida para manutenção, apresentação e evolução do sistema.

## 1. Visão Geral

O Counter é um sistema web e mobile para controle, movimentação e contagem de estoque.

A aplicação web administra produtos, categorias, fornecedores, usuários, movimentações, contagens, divergências, relatórios e auditoria. O aplicativo Android é usado pelo contador para visualizar contagens, registrar quantidades físicas, salvar dados localmente e sincronizar os itens com a API REST.

## 2. Tecnologias

- Back-end: PHP 8.2+ com Laravel 12
- Banco de dados: MySQL
- Front-end web: Blade, Tailwind CSS, Alpine.js, Lucide Icons e Vite
- API: REST com respostas JSON padronizadas
- Autenticação API: Laravel Sanctum
- Mobile: Android nativo com Kotlin
- Banco local mobile: Room Database
- Integração mobile: Retrofit e OkHttp
- Testes: PHPUnit
- Versionamento: Git e GitHub

## 3. Perfis de Usuário

### Administrador

Responsável pelo gerenciamento geral do sistema.

Permissões principais:

- Gerenciar usuários
- Gerenciar produtos
- Gerenciar categorias
- Gerenciar fornecedores
- Criar e acompanhar contagens de estoque
- Visualizar divergências
- Aprovar ajustes de estoque
- Acompanhar dashboard, relatórios e auditoria

### Estoquista

Responsável pelas operações diárias de estoque.

Permissões principais:

- Consultar produtos
- Registrar entradas de estoque
- Registrar saídas de estoque
- Registrar ajustes permitidos
- Consultar histórico de movimentações
- Exportar relatórios operacionais

### Contador

Responsável pela contagem física dos produtos.

Permissões principais:

- Acessar contagens disponíveis
- Visualizar itens da contagem
- Buscar itens por nome, SKU ou código de barras no app Android
- Informar quantidade física encontrada
- Salvar quantidades localmente no app
- Sincronizar itens contados com a API

## 4. Módulos do Sistema Web

### Autenticação

- Login com e-mail e senha
- Logout
- Rotas internas protegidas
- Mensagens de erro em português
- Estado de carregamento no botão de entrada

### Dashboard

- Total de produtos
- Total de categorias
- Total de fornecedores
- Movimentações recentes
- Contagens abertas ou em andamento
- Divergências
- Indicadores e gráficos

### Produtos

Campos principais:

- Nome
- Descrição
- SKU
- Código de barras
- Unidade de medida
- Preço de custo
- Preço de venda
- Quantidade atual
- Categoria
- Fornecedor
- Empresa

Regras:

- Cada produto pertence a uma empresa.
- SKU não pode se repetir dentro da mesma empresa.
- Código de barras não pode se repetir dentro da mesma empresa.
- A quantidade atual é alterada por movimentações ou ajustes aprovados.
- Produtos com movimentações ou contagens vinculadas não podem ser excluídos.

### Categorias

- Listagem
- Cadastro
- Edição
- Exclusão
- Bloqueio de exclusão quando houver produtos vinculados

### Fornecedores

- Listagem
- Cadastro
- Edição
- Exclusão
- Bloqueio de exclusão quando houver produtos vinculados

### Movimentações de Estoque

- Registro de entradas
- Registro de saídas
- Registro de ajustes
- Histórico com filtros
- Bloqueio de saída maior que o saldo disponível
- Atualização controlada da quantidade atual do produto

### Contagens de Estoque

- Criação de contagens
- Seleção manual de produtos
- Seleção de todos os produtos
- Controle de status: aberta, em andamento, finalizada e aprovada
- Atualização de itens pela web
- Atualização de itens pela API mobile
- Finalização apenas quando os itens necessários estiverem contados

### Divergências

- Listagem de faltas físicas
- Listagem de sobras físicas
- Itens sem divergência
- Filtros por tipo de divergência
- Aprovação de ajustes pelo administrador

### Relatórios

- Exportação de estoque em CSV
- Exportação de movimentações em CSV
- Exportação de divergências em CSV
- Filtros por produto, tipo, usuário, período e tipo de divergência

### Auditoria

- Registro de ações importantes
- Listagem de logs
- Filtros por módulo, ação, usuário e período
- Acesso restrito ao administrador

## 5. Componentes Globais de Interface

- Toast global com ícone, progress bar e tipos de sucesso, erro, informação e alerta
- Modal global de confirmação para exclusões e ações críticas
- Dropdown customizado
- Paginação padronizada
- Loader superior
- Skeletons reais por bloco
- Loader de tela inteira para ações demoradas
- Sidebar e header fixos
- Menu mobile para telas menores

## 6. Arquitetura Laravel

O projeto segue MVC com camadas auxiliares.

Fluxo principal:

```text
Route
Controller
Form Request
DTO
Service
Repository
Model
Banco MySQL
Blade ou JSON
```

Responsabilidades:

- Route: define URL, middleware e permissão.
- Controller: recebe requisições e orquestra o fluxo.
- Form Request: valida dados de entrada.
- DTO: transporta dados validados entre camadas.
- Service: concentra regras de negócio.
- Repository: isola consultas e persistência.
- Model: representa entidades e relacionamentos.
- View/API Response: entrega interface Blade ou JSON padronizado.

## 7. Estrutura do Banco

Tabelas principais:

- `companies`
- `users`
- `categories`
- `suppliers`
- `products`
- `stock_movements`
- `inventory_counts`
- `inventory_count_items`
- `audit_logs`
- `personal_access_tokens`

Relacionamentos principais:

- Uma empresa possui usuários, categorias, fornecedores, produtos, movimentações, contagens e logs.
- Uma categoria possui vários produtos.
- Um fornecedor possui vários produtos.
- Um produto possui várias movimentações.
- Um produto pode aparecer em vários itens de contagem.
- Uma contagem possui vários itens.
- Uma movimentação pode estar vinculada a uma contagem quando for ajuste aprovado.

## 8. API REST

A API é usada pelo aplicativo Android e por integrações externas de consulta.

Rotas implementadas:

```text
POST   /api/login
POST   /api/logout
GET    /api/me

GET    /api/products
GET    /api/products/{product}
GET    /api/products/search

GET    /api/mobile/summary
GET    /api/inventory-counts
GET    /api/inventory-counts/{inventoryCount}
GET    /api/inventory-counts/{inventoryCount}/items
POST   /api/inventory-counts/{inventoryCount}/items
POST   /api/inventory-counts/{inventoryCount}/sync
```

Padrão de resposta:

```json
{
  "success": true,
  "data": {},
  "message": "Operação realizada com sucesso"
}
```

Padrão de erro:

```json
{
  "success": false,
  "message": "Erro de validação",
  "errors": {}
}
```

## 9. Aplicativo Android

Objetivo principal: registrar contagens físicas de estoque.

Fluxo:

1. Usuário faz login no aplicativo.
2. Aplicativo salva o token de acesso.
3. Aplicativo consulta resumo e contagens disponíveis na API.
4. Usuário seleciona uma contagem.
5. Aplicativo lista os itens da contagem.
6. Usuário busca item por nome, SKU ou código de barras.
7. Usuário informa quantidade física encontrada.
8. Aplicativo salva a quantidade localmente com Room Database.
9. Aplicativo sincroniza os itens com a API REST.
10. Sistema web processa diferenças e permite aprovação de ajustes.

Arquitetura mobile:

```text
Activity
ViewModel
Repository
DAO
Room Database
Retrofit
API REST
```

## 10. Design Patterns

### Repository Pattern

Usado para separar acesso a dados da regra de negócio.

Classes:

- `ProductRepository`
- `StockMovementRepository`
- `InventoryCountRepository`

### Service Layer

Usado para concentrar regras de negócio.

Classes:

- `StockMovementService`
- `InventoryCountService`
- `AuditLogService`

### DTO

Usado para transportar dados validados entre camadas.

Classes:

- `ProductData`
- `StockMovementData`
- `InventoryCountData`

### Dependency Injection

Usado pelo container do Laravel para injetar services e repositories em controllers e outros services.

### Facade

Usado principalmente com `DB::transaction()` para garantir consistência em operações críticas.

## 11. Verificações

Comandos principais:

```powershell
cd backend
php artisan test
npm run build
composer audit
npm audit --audit-level=critical
git diff --check
```

```powershell
cd mobile
.\gradlew.bat assembleDebug
```

## 12. Próximas Evoluções Possíveis

Funcionalidades fora da primeira versão:

- Alerta de estoque mínimo
- Leitura de código de barras por câmera
- Emissão de notas fiscais
- Integração com sistemas externos de venda
- Controle financeiro completo
- Gestão de compras
