# API Mobile Counter

Contrato da API REST usada pelo aplicativo Android do Counter.

## Base

- Prefixo das rotas: `/api`
- Autenticação: Bearer Token via Laravel Sanctum
- Header obrigatório após login: `Authorization: Bearer {token}`
- Formato: JSON
- Perfis permitidos no mobile: `admin` e `counter`

## Resposta de Sucesso

```json
{
  "success": true,
  "data": {},
  "message": "Operação realizada com sucesso"
}
```

## Resposta Paginada

```json
{
  "success": true,
  "data": [],
  "message": "Registros encontrados com sucesso",
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 35
  }
}
```

## Resposta de Erro

```json
{
  "success": false,
  "message": "Erro de validação",
  "errors": {}
}
```

## Autenticação

### Login

`POST /api/login`

Body:

```json
{
  "email": "contador@counter.test",
  "password": "password"
}
```

Resposta:

```json
{
  "success": true,
  "data": {
    "token": "token",
    "user": {
      "id": 3,
      "name": "Contador",
      "email": "contador@counter.test",
      "role": "counter",
      "company": {
        "id": 1,
        "name": "Counter Demo"
      }
    }
  },
  "message": "Login realizado com sucesso"
}
```

### Usuário Autenticado

`GET /api/me`

### Logout

`POST /api/logout`

## Produtos

As rotas de produtos são usadas para consulta e busca. Elas são permitidas para `admin`, `stockist` e `counter`.

### Listar Produtos

`GET /api/products`

Query:

- `q`: busca por nome, SKU ou código de barras.
- `per_page`: quantidade por página, entre 1 e 100.

### Buscar Produtos

`GET /api/products/search`

Aceita os mesmos parâmetros de `/api/products`.

### Detalhar Produto

`GET /api/products/{product}`

Campos principais retornados:

- `id`
- `name`
- `description`
- `sku`
- `barcode`
- `unit`
- `cost_price`
- `sale_price`
- `current_quantity`
- `category`
- `supplier`

## Resumo Mobile

`GET /api/mobile/summary`

Retorna:

- `open_counts`
- `pending_items`
- `synced_items`
- `counted_items`
- `last_counted_at`

## Contagens

As rotas de contagens mobile são permitidas para `admin` e `counter`.

### Listar Contagens

`GET /api/inventory-counts`

Query:

- `status`: `open`, `in_progress`, `finished` ou `approved`.
- `per_page`: quantidade por página, entre 1 e 100.

Quando `status` não é informado, a API retorna contagens abertas e em andamento.

### Detalhar Contagem

`GET /api/inventory-counts/{inventoryCount}`

Campos principais retornados:

- `id`
- `title`
- `status`
- `items_count`
- `started_at`
- `finished_at`
- `approved_at`

### Listar Itens da Contagem

`GET /api/inventory-counts/{inventoryCount}/items`

Query:

- `sync_status`: `pending`, `synced` ou `error`.
- `per_page`: quantidade por página, entre 1 e 100.

Campos principais retornados:

- `id`
- `product`
- `system_quantity`
- `counted_quantity`
- `difference`
- `sync_status`
- `counted_at`

O campo `product` contém dados como nome, SKU, código de barras e unidade de medida.

### Enviar Itens Contados

`POST /api/inventory-counts/{inventoryCount}/items`

Body:

```json
{
  "items": [
    {
      "id": 1,
      "counted_quantity": 12.5
    }
  ]
}
```

### Sincronizar Itens Contados

`POST /api/inventory-counts/{inventoryCount}/sync`

Aceita o mesmo body de `/api/inventory-counts/{inventoryCount}/items`.

## Códigos de Erro Esperados

- `401`: usuário não autenticado.
- `403`: perfil sem permissão.
- `404`: recurso não encontrado ou pertence a outra empresa.
- `422`: erro de validação.
