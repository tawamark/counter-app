# Ordem de Desenvolvimento do Counter

Este arquivo registra a ordem seguida na construção do projeto e o estado atual de cada etapa. O sistema web, a API REST e o aplicativo Android já foram implementados.

## 1. Base do banco

Status: concluído.

- Criar migrations principais.
- Criar tabela `companies`.
- Ajustar tabela `users`.
- Criar tabela `categories`.
- Criar tabela `suppliers`.
- Criar tabela `products`.
- Criar tabela `stock_movements`.
- Criar tabela `inventory_counts`.
- Criar tabela `inventory_count_items`.
- Criar tabela `audit_logs`.
- Criar tabela `personal_access_tokens`.
- Aplicar índice único de SKU por empresa.
- Aplicar índice único de código de barras por empresa.

## 2. Models e relacionamentos

Status: concluído.

- Definir relações entre empresa e usuários.
- Definir relações entre empresa, produtos, categorias e fornecedores.
- Definir relações entre produtos e movimentações.
- Definir relações entre contagens e itens contados.
- Definir relações necessárias para auditoria e sincronização mobile.

## 3. Autenticação web

Status: concluído.

- Implementar login.
- Implementar logout.
- Proteger rotas internas.
- Identificar usuário autenticado nas operações.
- Padronizar mensagens de erro em português.
- Adicionar estado de carregamento no botão de entrada.

## 4. Perfis de usuário

Status: concluído.

- Criar perfis `admin`, `stockist` e `counter`.
- Aplicar regras de acesso por perfil.
- Restringir funcionalidades conforme perfil.
- Impedir exclusão do próprio usuário autenticado.
- Impedir alteração do próprio perfil de acesso.

## 5. Layout base web

Status: concluído.

- Criar layout interno padrão.
- Criar sidebar fixa.
- Criar header fixo.
- Criar menu mobile.
- Configurar componentes visuais com Tailwind CSS, Alpine.js e Lucide Icons.
- Aplicar paleta do projeto: `#FC6F20`, `#ffffff` e `#323232`.
- Aplicar logo do projeto.

## 6. Componentes globais de interface

Status: concluído.

- Criar toast global com ícones, progress bar e cores por tipo.
- Criar modal global de confirmação.
- Padronizar dropdowns customizados.
- Padronizar paginação.
- Criar loader global.
- Criar skeletons reais para blocos.
- Criar loader de tela inteira para ações demoradas.

## 7. Dashboard

Status: concluído.

- Exibir total de produtos.
- Exibir total de categorias.
- Exibir total de fornecedores.
- Exibir movimentações recentes.
- Exibir contagens abertas ou em andamento.
- Exibir divergências.
- Exibir gráficos e indicadores visuais.

## 8. Categorias

Status: concluído.

- Listar categorias.
- Cadastrar categorias.
- Editar categorias.
- Excluir categorias.
- Impedir exclusão quando houver produtos vinculados.

## 9. Fornecedores

Status: concluído.

- Listar fornecedores.
- Cadastrar fornecedores.
- Editar fornecedores.
- Excluir fornecedores.
- Impedir exclusão quando houver produtos vinculados.

## 10. Produtos

Status: concluído.

- Listar produtos.
- Cadastrar produtos.
- Editar produtos.
- Excluir produtos.
- Controlar nome, SKU, código de barras, categoria, fornecedor, preços e quantidade atual.
- Impedir SKU duplicado por empresa.
- Impedir código de barras duplicado por empresa.
- Impedir exclusão quando houver movimentações ou contagens vinculadas.

## 11. Movimentações de estoque

Status: concluído.

- Registrar entradas.
- Registrar saídas.
- Registrar ajustes.
- Atualizar quantidade do produto de forma controlada.
- Impedir saídas inválidas quando não houver saldo suficiente.
- Registrar histórico da movimentação.

## 12. Histórico de movimentações

Status: concluído.

- Listar movimentações.
- Filtrar por produto.
- Filtrar por tipo.
- Filtrar por usuário.
- Filtrar por período.

## 13. Contagem de estoque

Status: concluído.

- Criar contagens.
- Selecionar todos os produtos.
- Selecionar produtos específicos.
- Controlar status da contagem.
- Permitir status aberta, em andamento, finalizada e aprovada.
- Finalizar contagem somente quando os itens necessários estiverem contados.

## 14. Itens da contagem

Status: concluído.

- Registrar quantidade do sistema.
- Registrar quantidade contada.
- Calcular diferença entre quantidade contada e quantidade registrada.
- Permitir limpeza da quantidade contada.
- Atualizar itens pela interface web.
- Atualizar itens pela API mobile.

## 15. Divergências

Status: concluído.

- Listar produtos com diferença.
- Mostrar sobra física.
- Mostrar falta física.
- Separar itens sem divergência dos itens divergentes.
- Filtrar divergências por tipo.

## 16. Aprovação de ajustes

Status: concluído.

- Permitir que o administrador aprove ajustes.
- Gerar movimentação de ajuste.
- Atualizar quantidade atual do produto.
- Manter rastreabilidade da aprovação.

## 17. Relatórios

Status: concluído.

- Criar tela de relatórios.
- Exportar estoque em CSV.
- Exportar movimentações em CSV.
- Exportar divergências em CSV.
- Aplicar filtros nos relatórios.
- Organizar a tela de relatórios em blocos por linha.

## 18. Auditoria

Status: concluído.

- Registrar ações importantes.
- Listar logs de auditoria.
- Filtrar logs por módulo, ação, usuário e período.
- Restringir auditoria ao perfil administrador.

## 19. API REST

Status: concluído.

- Criar login para o aplicativo mobile.
- Criar logout por token.
- Criar rota de usuário autenticado.
- Criar rota para listar produtos.
- Criar rota de busca de produtos.
- Criar rota de resumo mobile.
- Criar rota para listar contagens.
- Criar rota para listar itens de contagem.
- Criar rota para enviar itens contados.
- Criar rota para sincronização.
- Padronizar respostas JSON de sucesso e erro.
- Proteger rotas por autenticação e perfil.

## 20. Seeders

Status: concluído.

- Criar empresa padrão.
- Criar usuários de demonstração.
- Criar categorias de exemplo.
- Criar fornecedores de exemplo.
- Criar produtos de exemplo.
- Criar movimentações de exemplo.
- Criar contagens e divergências de exemplo.
- Criar dados suficientes para demonstração acadêmica.

## 21. Aplicativo Android

Status: concluído.

- Criar projeto Android nativo em Kotlin.
- Implementar login.
- Integrar login com API REST.
- Listar resumo mobile.
- Listar contagens disponíveis.
- Exibir status das contagens com indicador visual.
- Listar itens da contagem.
- Buscar itens por nome, SKU ou código de barras.
- Registrar quantidades contadas.
- Salvar dados locais com Room Database.
- Sincronizar dados com a API REST.
- Exibir toasts centralizados.
- Exibir loaders e estados vazios.
- Criar navegação inferior.
- Melhorar usabilidade no emulador Android.

## 22. Testes e validações

Status: concluído.

- Criar testes de DTOs.
- Criar testes de autenticação.
- Criar testes de perfis de acesso.
- Criar testes de produtos, categorias e fornecedores.
- Criar testes de movimentações.
- Criar testes de contagens e divergências.
- Criar testes de relatórios.
- Criar testes de auditoria.
- Criar testes da API REST.
- Criar testes dos componentes globais de toast e confirmação.
- Compilar aplicativo Android com Gradle.

## 23. Documentação e entrega

Status: concluído.

- Atualizar README principal.
- Atualizar README do backend.
- Manter `AGENTS.md` fora do Git.
- Manter documentação acadêmica original fora do Git.
- Atualizar documento técnico final.
- Inserir prints da aplicação web.
- Inserir prints do aplicativo Android.
- Garantir que os Design Patterns estejam explicados com nomes reais das classes.
- Garantir que a API documentada bata com as rotas reais.
