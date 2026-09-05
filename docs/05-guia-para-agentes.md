# Guia para Agentes (e Humanos) — systemPHP

Este documento existe para que qualquer agente de IA (Claude, Copilot,
etc.) ou pessoa nova no projecto produza código consistente com as
decisões já tomadas, sem precisar de reler toda a conversa que as
originou.

Se estiveres a gerar código para este projecto, lê isto primeiro.

## Contexto essencial

- Projecto Laravel, com dois objectivos simultâneos: aprendizagem
  prática de PHP/Laravel e construção de um produto real. Código deve
  ser tratado com o rigor de produção, não como exercício descartável.
- Ver `01-visao-geral.md` para a motivação completa e o estado do
  domínio de negócio (ainda pendente de definição).
- Existe um projecto irmão, o **KwanzaFolha Cloud**, em Node/TypeScript
  — não relacionado no código, mas partilha filosofia de arquitectura em
  camadas e rigor. Não confundir os dois nem misturar código de um no
  outro.

## Decisões já fechadas — não sugerir alternativas sem pedido explícito

- Base de dados: **MySQL 8.4**, via Docker. Não sugerir PostgreSQL,
  SQLite ou Oracle.
- Autenticação de API: **Laravel Sanctum**. Não sugerir Passport ou JWT
  manual sem justificação explícita para mudar.
- Testes: **Pest**. Não escrever testes em sintaxe PHPUnit clássica
  (`extends TestCase` com métodos `test*`) — usar a sintaxe funcional do
  Pest (`it(...)`, `test(...)`).
- Formato: **Laravel Pint**. Não introduzir outra ferramenta de
  formatação (PHP-CS-Fixer directo, StyleCI, etc.).
- Análise estática: **Larastan**.
- Documentação da API: **Scramble** (gerada do código, não anotações
  manuais tipo `@openapi`).
- Nome do projecto: sempre lido de `config('app.name')`. Nunca
  hardcoded em código, views ou mensagens de erro.
- API sempre versionada: `/api/v1/...`. Nunca criar rotas de API sem
  prefixo de versão.

## Regras de arquitectura — obrigatórias

1. **Controllers nunca contêm lógica de negócio.** Um Controller recebe
   um Form Request já validado, chama uma Action, devolve um Resource.
   Se um Controller tiver mais do que ~10 linhas de corpo por método,
   é sinal de que lógica deveria estar numa Action.

2. **Toda a validação de input passa por um Form Request dedicado.**
   Nunca validar inline com `$request->validate([...])` dentro do
   Controller — criar sempre uma classe em `app/Http/Requests/`.

3. **Toda a resposta JSON da API passa por um API Resource.** Nunca
   devolver um Model Eloquent directamente (`return $empresa;`) — sempre
   `return new EmpresaResource($empresa);`. Isto desacopla o schema da
   base de dados do contrato público da API.

4. **Toda a Action nova precisa de teste.** Pelo menos um teste de
   Feature (Pest) cobrindo o caminho principal. Não considerar uma
   funcionalidade "pronta" sem isto.

5. **Erros são tratados centralmente**, no Exception Handler
   customizado — nunca com `try/catch` espalhados nos Controllers a
   devolver respostas ad-hoc. Excepções de domínio devem ter uma classe
   própria (ex: `EmpresaNaoEncontradaException`) capturada e traduzida
   para uma resposta HTTP consistente no Handler.

6. **Migrations são sempre aditivas em ambientes já usados.** Nunca
   editar uma migration já corrida em qualquer ambiente partilhado —
   criar uma nova migration para alterações.

7. **Sem emojis em código, comentários de código, ou ficheiros do
   projecto** (incluindo esta documentação). Convenção herdada
   deliberadamente do projecto KwanzaFolha.

8. **Comentários e nomes de variáveis/métodos em português**, seguindo
   o padrão já usado no domínio (quando o domínio de negócio for
   finalizado). Nomes de classes, métodos do framework, e convenções do
   Laravel em si mantêm-se em inglês (é a convenção da própria
   linguagem/framework).

## Fluxo de trabalho esperado

- Mudanças são feitas por etapas pequenas, cada uma testada e
  confirmada antes de avançar. Não produzir uma funcionalidade completa
  de uma só vez sem checkpoints.
- Ao introduzir uma ferramenta ou pacote novo, explicar sempre: para
  que serve, porque essa e não outra alternativa, e que vantagem traz —
  seguindo o mesmo formato usado em `04-stack-e-ferramentas.md`.
- Qualquer decisão nova de arquitectura deve ser reflectida em
  `03-arquitetura.md` (secção "Estado das decisões"), não apenas
  mencionada em conversa.

## O que fazer perante ambiguidade

- Se o domínio de negócio ainda não estiver definido e for necessário
  para a tarefa pedida, perguntar explicitamente antes de assumir um
  domínio (não presumir que é o mesmo do KwanzaFolha sem confirmação).
- Se uma tarefa pedida contradisser uma das "decisões já fechadas"
  acima, sinalizar a contradição explicitamente antes de implementar,
  em vez de silenciosamente escolher uma das duas opções.
