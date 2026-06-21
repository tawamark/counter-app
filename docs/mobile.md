# Aplicativo Mobile Counter

O aplicativo mobile do Counter foi criado em Android nativo com Kotlin, seguindo o padrão apresentado em aula e integrando com a API REST do Laravel.

## Objetivo

Permitir que o perfil Contador acesse contagens de estoque, registre quantidades físicas no celular, salve os dados localmente e sincronize os itens contados com a API REST.

## Tecnologias

- Android nativo
- Kotlin
- XML com ViewBinding
- AppCompatActivity
- ViewModel
- Repository
- Room Database
- DAO
- Retrofit
- OkHttp
- RecyclerView
- Gradle Kotlin DSL
- KSP

## Arquitetura

```text
Activity
ViewModel
Repository
API REST / DAO
Room Database
```

Responsabilidades:

- Activity: interface e interação com o usuário.
- ViewModel: estado da tela e chamadas assíncronas.
- Repository: acesso à API e ao banco local.
- DAO: operações no banco local.
- Room Database: armazenamento local das contagens e itens.
- Retrofit: comunicação com a API REST do Laravel.

## Telas Implementadas

- Login.
- Resumo mobile.
- Lista de contagens.
- Itens da contagem.
- Busca por nome, SKU ou código de barras.
- Registro local da quantidade contada.
- Sincronização de itens contados.

## Recursos de Interface

- Toast centralizado para sucesso, erro, informação e alerta.
- Loader ao atualizar contagens.
- Estados vazios.
- Barra inferior com ações principais.
- Indicadores visuais de status nas contagens.
- Indicadores visuais de sincronização nos itens da contagem.
- Botão de voltar nas telas internas.

## API Consumida

Base URL padrão para emulador Android:

```text
http://10.0.2.2:8000/api/
```

Rotas utilizadas:

```text
POST /api/login
POST /api/logout
GET  /api/mobile/summary
GET  /api/inventory-counts
GET  /api/inventory-counts/{inventoryCount}/items
POST /api/inventory-counts/{inventoryCount}/sync
```

## Persistência Local

O aplicativo usa Room Database para salvar:

- Contagens disponíveis.
- Itens da contagem.
- Quantidades digitadas pelo contador.
- Status local de sincronização.

Isso permite manter as quantidades no dispositivo antes do envio para o servidor.

## Como Compilar

Com Android SDK e JDK 17 configurados:

```powershell
cd mobile
.\gradlew.bat assembleDebug
```

O APK debug é gerado em:

```text
mobile/app/build/outputs/apk/debug/app-debug.apk
```

## Como Testar no Emulador

1. Subir o MySQL.

2. Subir o Laravel:

```powershell
cd backend
php artisan serve --host=0.0.0.0 --port=8000
```

3. Instalar o APK no emulador:

```powershell
adb install -r mobile/app/build/outputs/apk/debug/app-debug.apk
```

4. Entrar com usuário contador:

```text
contador@counter.test
password
```

## Observações

- O emulador Android acessa o servidor local pelo endereço `10.0.2.2`.
- Para testar em celular físico, a base URL deve apontar para o IP da máquina na rede local.
- Os itens contados são salvos localmente no Room antes da sincronização.
- A leitura por câmera não faz parte da primeira versão; a busca usa texto por nome, SKU ou código de barras.
