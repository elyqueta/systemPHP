# AGENTS.md — SYSTEM (antigo systemPHP)

Este ficheiro é o ponto de entrada para qualquer agente de IA (Kilo Code,
Claude, Copilot, etc.) que trabalhe neste repositório. Lê isto primeiro,
depois lê o ficheiro da fase corrente em `docs/fases/`.

## O que mudou (10/2026)

Este projecto deixou de ser um exercício de aprendizagem PHP genérico e
passou a ser a implementação da **Fase 1 (ERP/RP Core)** de uma
plataforma maior chamada **SYSTEM**, descrita em dois documentos de
arquitectura na raiz do projeto (`arquitetura_erp_tecnologias_roadmap.pdf`
e `SYSTEM_Plataforma_Empresarial_Roadmap_Estrategico.pdf`). Isso reverte
decisões que estavam registadas como "fechadas" em `docs/`:

| Decisão antiga (docs/03 e docs/04) | Decisão nova                                    |
| ------------------------------------ | ------------------------------------------------ |
| MySQL 8.4                            | **PostgreSQL** (fonte de verdade)                 |
| Domínio de negócio "pendente"        | **HR/Payroll**, reaproveitando o domínio já modelado no KwanzaFolha Cloud (Node/TypeScript) |
| Entidade principal: `empresas`       | **`institutions`** (conceito "Instituição", identificador em inglês) |
| Nomes de tabelas/colunas/rotas em português | **Inglês**, ver secção "Convenção de nomenclatura" abaixo |

Os ficheiros `docs/01` a `docs/04` antigos ainda descrevem correctamente
a filosofia de camadas (Route → Form Request → Controller → Action →
Model → Resource) e continuam válidos. O que está desactualizado neles é
a base de dados, o estado "pendente" do domínio, e o idioma dos
identificadores de código.

## Convenção de nomenclatura (decisão fechada em 12/09/2026)

Esta é a regra mais importante desta reorientação e aplica-se a **todo o
código novo**, sem excepção:

| Camada                                   | Idioma       | Exemplo                                    |
| ------------------------------------------ | ------------ | ------------------------------------------- |
| Nomes de tabelas e colunas                | **Inglês**   | `institutions`, `tax_id`, `founding_date`   |
| Nomes de Models, Controllers, Actions      | **Inglês**   | `Institution`, `CreateInstitutionAction`    |
| Rotas de API e nomes de rotas              | **Inglês**   | `/api/v1/institutions`                      |
| Comentários de código                     | **Português**| `// resolve o bug das "instituições fantasmas"` |
| Documentação (`docs/`, `AGENTS.md`, `HUMAN.md`) | **Português** | este próprio ficheiro                  |
| Mensagens de erro devolvidas pela API     | **Português**| `"Credenciais inválidas."`                  |
| Nomes de variáveis dentro do código PHP   | **Inglês**   | `$institution`, não `$instituicao`          |

Regra prática para decidir um caso não previsto nesta tabela: **se
aparece num identificador que o PHP/Postgres lê (nome de coluna, classe,
método, rota), é inglês. Se é texto para um humano ler (comentário,
string de erro, documentação), é português.**

Isto substitui a regra antiga em `docs/05-guia-para-agentes.md`
("nomes de domínio em português") — essa regra falava de identificadores
de código, que agora é inglês. As mensagens que um humano lê continuam
em português pt-AO, como sempre.

## Ordem de leitura obrigatória para qualquer tarefa

1. `docs/00-decisoes-revisadas.md` — o porquê da mudança de direcção.
2. `docs/fases/FASE-N-*.md` correspondente à fase em que o projecto está.
3. `HUMAN.md` — como o Ely (humano) valida cada etapa. Cada fase termina
   num **checkpoint de confirmação explícita**; nenhum agente deve
   avançar para a fase seguinte sem essa confirmação ter sido registada.
4. Os `docs/01` a `docs/05` antigos, só como referência de arquitectura
   em camadas e convenções de código (Pint, Pest, Larastan, Scramble).

## Regras que continuam válidas (herdadas do systemPHP original)

- Controllers nunca contêm lógica de negócio — vive em Actions.
- Toda a validação passa por Form Requests dedicados.
- Toda a resposta de API passa por um API Resource — nunca devolver um
  Model Eloquent directamente.
- Toda a Action nova ganha pelo menos um teste de Feature em Pest.
- API sempre versionada: `/api/v1/...`.
- Sem emojis em código, comentários ou documentação.
- Migrations são sempre aditivas em qualquer ambiente já usado por
  alguém — nunca editar uma migration já corrida.
- Trabalho por etapas pequenas, cada uma confirmada antes de avançar.

## Regras novas, específicas desta reorientação

- **PostgreSQL é a única base de dados suportada.** Não escrever SQL
  específico de MySQL. Onde o Eloquent abstrai a diferença, preferir
  sempre a API do Eloquent a SQL cru.
- **Tabela `users` nativa do Laravel, não uma tabela `Utilizador`
  customizada.** Com os identificadores todos em inglês, deixou de fazer
  sentido substituir o scaffold nativo — só se acrescentam as colunas
  que faltam (`uuid`, `active`, `last_login_at`) via migration aditiva.
- **Arquitectura de IDs híbrida**, herdada do KwanzaFolha: toda a tabela
  exposta pela API tem `id` (BIGINT interno, nunca exposto) e `uuid`
  (chave pública, usada nas rotas e nos Resources), via trait
  `HasUuid`.
- **RBAC via `spatie/laravel-permission`**, com nomes de papel também em
  inglês (`super_admin`, `institution_manager`).
- **Nunca implementar cálculo de IRT/INSS antes de a tabela oficial ser
  validada linha a linha pelo Ely.** Mesmo já tendo recebido uma
  imagem da tabela, dois valores (1º e 2º escalão) ainda estão por
  confirmar por inconsistência aritmética com o resto da tabela — ver
  `docs/00-decisoes-revisadas.md`, secção 7.

## Onde o Kilo Code deve parar e pedir confirmação

Cada `docs/fases/FASE-N-*.md` tem uma secção "Checkpoint" no fim. Um
agente autónomo (Kilo Code) deve, ao chegar lá, parar de escrever código
e apresentar ao Ely exactamente o que o `HUMAN.md` pede para essa fase
— nunca assumir "sim" implícito e continuar para a fase seguinte.
