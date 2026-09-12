# Decisões Revistas — de "systemPHP" para "SYSTEM"

Este documento existe para que ninguém (humano ou agente de IA) fique
confuso ao ler `docs/01` a `docs/04`, que descrevem decisões agora
parcialmente ultrapassadas. Não apagámos esses ficheiros porque a parte
de arquitectura em camadas continua correcta — só a base de dados e o
domínio de negócio mudaram.

## 1. Base de dados: MySQL → PostgreSQL

### Porquê a mudança

O documento `arquitetura_erp_tecnologias_roadmap.pdf` (fornecido pelo
Ely) define PostgreSQL como "a base de dados relacional principal" de
um ERP novo, citando como motivos: integridade referencial forte,
capacidade de lidar com relacionamentos complexos, e adequação a UUIDs
e a modelação de dados financeiros. Isto substitui o argumento anterior
(`docs/04-stack-e-ferramentas.md`), que escolhia MySQL por custo de
hospedagem partilhada.

### Porque é uma mudança de baixo risco, tecnicamente

Ao inspeccionar o repositório, confirmámos que:

- Não existe ainda um `.env` real, só `.env.example` com
  `DB_CONNECTION=sqlite` (o default de instalação do Laravel).
- `config/database.php` já vem com um bloco `pgsql` completo e correcto
  de fábrica — não precisa de nenhuma alteração de código.
- Não existe nenhuma migration de domínio de negócio ainda (só as
  tabelas base do Laravel: `users`, `cache`, `jobs`).

Ou seja: a troca é apenas em `docker-compose.yml` (imagem `postgres`
em vez de `mysql:8.4`) e nas variáveis do `.env`. Nenhum código PHP
precisa de mudar por causa disto.

### O que fica pendente para a Fase 0

- Trocar `docker-compose.yml` para usar `postgres:16-alpine` (mesma
  imagem já usada com sucesso no KwanzaFolha Cloud).
- Adicionar `ext-pgsql` e `ext-pdo_pgsql` à lista de extensões PHP no
  `docs/02-instalacao.md` (a secção de extensões Ubuntu lista só
  `php8.3-mysql` — precisa de `php8.3-pgsql`).
- Actualizar `docs/02-instalacao.md`, `docs/03-arquitetura.md` e
  `docs/04-stack-e-ferramentas.md` para dizer PostgreSQL em vez de
  MySQL. Isto é tarefa explícita da Fase 0 (ver
  `docs/fases/FASE-0-fundacao-e-ambiente.md`), não algo para fazer
  "de repente" a meio de outra tarefa.

## 2. Domínio de negócio: de "pendente" para "HR/Payroll"

O `docs/01-visao-geral.md` deixava o domínio de negócio como decisão
em aberto. Ficou agora fechado: **vamos reaproveitar o domínio já
modelado e testado no KwanzaFolha Cloud** (gestão de RH e folha
salarial para empresas angolanas, com INSS e IRT). A vantagem, tal como
o próprio `01-visao-geral.md` já antecipava, é que o esforço de
aprendizagem de Laravel não se mistura com o esforço de aprender regras
fiscais novas — as regras já são conhecidas.

Isto **não inclui ainda o cálculo de IRT/INSS em si** (o módulo
`04_payroll`). A Fase 1 aqui descrita cobre apenas o equivalente ao
módulo `01_core` do KwanzaFolha: instituições, utilizadores,
configuração fiscal (as taxas, não a lógica de cálculo) e contas
bancárias.

## 3. Renomeação: `empresas` → `instituicoes`

Esta é a única decisão nova que **não foi ainda confirmada
explicitamente pelo Ely** e que bloqueia o início da Fase 1. Está
sinalizada como o primeiro checkpoint em `HUMAN.md`.

### Porquê propor a mudança

O `SYSTEM_Plataforma_Empresarial_Roadmap_Estrategico.pdf` define o
modelo conceptual da plataforma assim:

> Plataforma → Instituição → Departamento → Equipa → Pessoa → Cargo

E o modelo de utilizadores inicial é explicitamente "Admin da
Plataforma" e "Gestor da **Instituição**" — nunca "Empresa". Como o
SYSTEM é pensado para crescer para além de RH/Payroll (projectos,
carreira, ecossistema), o nome "empresa" ficaria estranho mais tarde
quando a plataforma também servir organizações que não são
necessariamente "empresas" no sentido comercial.

### Impacto técnico da renomeação

Isto afecta directamente a tradução do schema do KwanzaFolha
(`db/01_core/002_empresas.sql`, `003_empresa_config_fiscal.sql`,
`004_empresa_contas_bancarias.sql`, `005_utilizador_empresa.sql`):

| Tabela KwanzaFolha (Postgres/Node) | Tabela SYSTEM (Postgres/Laravel, inglês) |
| ----------------------------------- | ------------------------------------------ |
| `empresas`                          | `institutions`                             |
| `empresa_config_fiscal`             | `institution_tax_configurations`           |
| `empresa_contas_bancarias`          | `institution_bank_accounts`                |
| `utilizador_empresa`                | `institution_user`                         |
| `utilizadores`                      | `users` (tabela nativa do Laravel, estendida) |

**Actualização de 12/09/2026**: o Ely confirmou manter o conceito
"Instituição", mas decidiu (ver secção 6) que todo o código passa a usar
identificadores em inglês. "Instituição" continua a ser o nome pelo qual
falamos do conceito em português; a tabela, o Model, as rotas e as
colunas usam `institution`. Os campos internos também são traduzidos —
lista completa em `docs/fases/FASE-1-schema-core-postgresql.md`.

## 4. Arquitectura híbrida de IDs (herdada, sem alterações)

O KwanzaFolha já resolveu isto bem e vamos repetir o padrão em Laravel:

- Toda a tabela pública da API tem `id` (BIGINT, `bigserial` no
  Postgres, chave primária interna — nunca aparece numa URL) e `uuid`
  (gerado automaticamente, chave pública).
- Em Eloquent, isto é um trait reutilizável (`HasUuid`) que gera o UUID
  no evento `creating` do model e expõe uma route-model-binding
  personalizada por `uuid` em vez de `id`. Isto fica detalhado na
  Fase 1.

## 5. RBAC: `spatie/laravel-permission`

O KwanzaFolha usa uma coluna `role` livre (`SUPER_ADMIN`, `ADMIN`, etc.)
com um middleware `permitirRoles(...)`. Isto foi suficiente para um
MVP, mas o roadmap técnico pede explicitamente "permissões granulares
por módulo e ação" (exemplos dados: `sales.view`, `finance.approve`).
Isso é exactamente o que `spatie/laravel-permission` resolve de forma
madura em Laravel — é a biblioteca de facto para isto, com mais de uma
década de manutenção activa, e integra-se directamente com o sistema de
Gates/Policies nativo do Laravel (que os `docs/03-arquitetura.md`
originais já mencionam como responsabilidade da camada
"Exception/Policy").

Não vamos escrever RBAC à mão. Detalhes de instalação e modelação na
Fase 2.

## 6. Identificadores de código em inglês, documentação em português

Decisão fechada a 12/09/2026: **todo identificador que o PHP ou o
Postgres interpretam (tabelas, colunas, classes, métodos, rotas) fica
em inglês.** Tudo o que é texto para um humano ler (comentários,
mensagens de erro da API, ficheiros em `docs/`) continua em português
pt-AO, exactamente como sempre foi neste projecto e no KwanzaFolha.

### Porquê

Identificadores em inglês é o padrão de facto de qualquer biblioteca,
framework ou serviço open-source que este projecto vai inevitavelmente
consumir ou, um dia, expor a terceiros (o roadmap estratégico fala em
"Fase 7 — Ecossistema", que implica integrações externas). Misturar
`instituicao_id` com `created_at` nativo do Eloquent já era uma
inconsistência a prazo; alinhar tudo em inglês resolve isso de vez.

### Efeito colateral positivo: tabela `users` nativa

Com os identificadores em inglês, deixou de haver razão para substituir
o scaffold de autenticação nativo do Laravel (`users`, Model `User`)
por um equivalente `utilizadores`/`Utilizador` customizado, como a
Fase 2 original previa. Isto **remove complexidade**, não acrescenta:
menos um Model para manter, `config/auth.php` não precisa de nenhuma
alteração, e qualquer pacote do ecossistema Laravel que assuma
`App\Models\User` (há muitos) continua a funcionar sem adaptação. Só
acrescentamos, via migration aditiva, as colunas que a tabela nativa
não tem: `uuid`, `active`, `last_login_at`.

## 7. Tabela do IRT (Anexo I, dezembro de 2025) — pendências antes da Fase 5

O Ely enviou uma fotografia da tabela oficial de escalões do IRT
(Anexo I, referente ao artigo 21.º, publicada no Diário da República de
30 de dezembro de 2025). Antes de qualquer migration ou seed com estes
valores ser escrita, três pontos continuam por confirmar:

1. **Escalões 1 e 2 não reconciliam matematicamente com o resto da
   tabela.** A fórmula `parcela_fixa[N] = parcela_fixa[N-1] +
   taxa[N-1] × largura[N-1]` bate certo do 3º ao 11º escalão, mas falha
   nos dois primeiros. Pode ser erro de transcrição/OCR da foto, ou o
   legislador pode mesmo ter definido esses dois escalões fora da
   fórmula estrita. Precisa de confirmação visual directa da imagem
   antes de fixarmos os valores num seed.
2. **Data de vigência** desta tabela — a partir de que data se aplica,
   e se substitui integralmente a tabela de 2024 já referenciada no
   `db/04_payroll` (ainda por criar) do KwanzaFolha.
3. **Taxas de INSS** (actualmente 3% funcionário / 8% entidade, valores
   por omissão em `institution_tax_configurations`) — confirmar se
   continuam as mesmas ou se também mudaram nesta actualização.

Enquanto estes três pontos não estiverem confirmados, não existe
`docs/fases/FASE-5-*.md`. Não escrever nenhuma lógica de cálculo fiscal
com base em suposições.
