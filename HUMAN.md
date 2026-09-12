# HUMAN.md — Guia de Validação para o Ely

Este ficheiro é para ti, não para o Kilo Code. Explica, fase a fase, o
que correr no terminal, o que deves ver, e o que responder de volta
(a mim ou ao Kilo Code) para autorizar a fase seguinte.

Regra geral: **nenhuma fase avança sem o teu "sim" explícito**. Se
correres um comando e o resultado não bater certo com o que este guia
descreve, pára e cola o erro — não deixes o agente "tentar corrigir
sozinho" sem te mostrar o que mudou.

---

## Decisões já fechadas (não precisas de repetir estas confirmações)

- Entidade principal chama-se **Institution** no código (`instituicao`
  como conceito em português, `institution`/`institutions` em
  tabelas, Models e rotas).
- **Todo identificador de código é em inglês** (tabelas, colunas,
  Models, rotas). Comentários, documentação e mensagens de erro
  continuam em português. Detalhe completo em
  `docs/00-decisoes-revisadas.md`, secção 6.
- A tabela `users` nativa do Laravel é reaproveitada directamente — não
  há Model `Utilizador` customizado.

## Pendência bloqueante: tabela do IRT (Fase 5 ainda não existe)

Enviaste uma foto da tabela oficial de escalões do IRT, mas três pontos
continuam por confirmar antes de eu poder escrever
`docs/fases/FASE-5-*.md` (ver `docs/00-decisoes-revisadas.md`,
secção 7):

1. Os valores do 1º e 2º escalão na foto não reconciliam
   matematicamente com o resto da tabela — confirma se são mesmo esses
   os números, olhando de novo para a imagem.
2. Data de vigência da tabela.
3. Se as taxas de INSS (3%/8%) continuam iguais.

Sem isto, a Fase 5 não avança — não vou adivinhar valores fiscais.

---

## Fase 0 — Fundação e Ambiente

Ficheiro: `docs/fases/FASE-0-fundacao-e-ambiente.md`

### O que correr

```bash
docker compose up -d
docker compose ps
php -m | grep pgsql
composer install
php artisan key:generate
php artisan migrate:status
```

### O que deves ver

- `docker compose ps` — o serviço `db` com estado `healthy`, imagem
  `postgres:16-alpine`.
- `php -m | grep pgsql` — duas linhas: `pgsql` e `pdo_pgsql`. Se não
  aparecer nada, falta instalar `php8.3-pgsql` (comando está no
  ficheiro da fase).
- `php artisan migrate:status` — uma tabela com `users`, `cache`,
  `jobs`, mais linhas novas de `sanctum` e `permission` (dos pacotes
  instalados nesta fase). Todas podem estar como "Ran" ou "Pending",
  desde que não haja nenhum erro de ligação à base de dados.

### O que me confirmares para avançar

Cola-me o output de `docker compose ps` e de `php artisan migrate:status`.
Se ambos estiverem como descrito acima, avanças para a Fase 1.

---

## Fase 1 — Schema Core em PostgreSQL

Ficheiro: `docs/fases/FASE-1-schema-core-postgresql.md`

### O que correr

```bash
php artisan migrate:fresh
php artisan tinker --execute="
  \$i = App\Models\Institution::create(['name' => 'Kwanza Tech Lda', 'tax_id' => '5000000001']);
  echo \$i->uuid . PHP_EOL;
  echo \$i->id . PHP_EOL;
"
```

### O que deves ver

Duas linhas no output do `tinker`: a primeira um UUID (algo como
`a1b2c3d4-e5f6-...`), a segunda um número pequeno (`1`). Se der erro de
"relation institutions does not exist", a migration não correu — corre
`php artisan migrate:fresh` outra vez e lê o erro completo antes de
tentares de novo.

### Verificação extra (opcional, mas recomendada)

Entra directamente no Postgres e confirma visualmente a tabela:

```bash
docker compose exec db psql -U systemphp_app -d systemphp -c "\d institutions"
```

Deve listar as colunas `id`, `uuid`, `name`, `tax_id`, etc., com `id`
como `bigint` e `uuid` como tipo `uuid`.

### O que me confirmares para avançar

Cola-me o UUID e o `id` gerados pelo `tinker`. Se baterem certo com o
esperado, avanças para a Fase 2.

---

## Fase 2 — Autenticação e RBAC

Ficheiro: `docs/fases/FASE-2-autenticacao-rbac.md`

### O que correr

```bash
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\SuperAdminSeeder
php artisan serve
```

Noutro terminal:

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@system.ao","password":"ChangeMe123!"}'
```

### O que deves ver

Uma resposta JSON com `token` (uma string longa) e `user` com
`"roles": ["super_admin"]`.

### Atenção de segurança antes de avançares

A password `ChangeMe123!` só é aceitável nesta tua máquina de
desenvolvimento. Antes de qualquer partilha do projecto (mesmo que só
para um colega testar), define `SEED_ADMIN_PASSWORD` no `.env` com uma
password forte e recorre o seeder.

### O que me confirmares para avançar

Cola-me a resposta do `curl` (podes ocultar o valor exacto do token,
só confirma que existe e que `roles` mostra `super_admin`).

---

## Fase 3 — Módulo Institutions (CRUD completo)

Ficheiro: `docs/fases/FASE-3-modulo-instituicoes.md`

### O que correr

```bash
php artisan test
php artisan pint --test
./vendor/bin/phpstan analyse
```

### O que deves ver

- `php artisan test` — todos os testes passam (incluindo os dois novos
  de `InstitutionTest`: super admin consegue criar, utilizador normal é
  recusado).
- `php artisan pint --test` — sem ficheiros a precisar de formatação.
- `phpstan analyse` — sem erros (ou só avisos conhecidos, que o agente
  deve justificar explicitamente, não silenciar).

### Verificação manual (recomendada)

Acede a `http://localhost:8000/docs/api` no browser (Scramble) e
confirma que os endpoints `/api/v1/institutions` aparecem documentados
automaticamente, com os campos que defini nos Form Requests.

### O que me confirmares para avançar

Cola-me o resumo final do `php artisan test` (quantos testes passaram)
e confirma se conseguiste ver a documentação Scramble no browser.

---

## Fase 4 — Backoffice Blade

Ficheiro: `docs/fases/FASE-4-backoffice-blade-design-system.md`

### Decisão pendente antes de começar

Este é o único ponto onde preciso de algo teu antes de o Kilo Code
gerar código com estilo definitivo: **o CSS real do frontend React do
KwanzaFolha** (onde estão as variáveis `--glass-*`), para não
inventarmos cores que depois têm de ser todas trocadas. Se preferires
avançar já com os valores provisórios descritos no ficheiro da fase,
diz isso explicitamente e eu sigo em frente — só não quero assumir isso
silenciosamente.

### O que correr (depois da decisão acima)

```bash
npm install
npm run build
php artisan serve
```

Acede a `http://localhost:8000/admin/login`, entra com o super admin, e
confirma que consegues ver a listagem de Institutions.

### O que me confirmares para avançar

Uma captura de ecrã (ou apenas confirmação por texto) de que o login e
a listagem funcionam. A partir daqui, decidimos juntos se a Fase 5 é
"polir o design system" ou já avançar para o próximo módulo de negócio
do roadmap (`docs/00-decisoes-revisadas.md` tem a tabela de fases de
produto completa, para referência do que vem depois).

---

## Como falar com o Kilo Code entre fases

Sugestão de fluxo prático, para não haver ambiguidade sobre "o que já
foi feito": no início de cada sessão nova com o Kilo Code, diz-lhe
explicitamente em que fase estás (ex.: "estamos na Fase 2, a Fase 1 já
foi validada por mim") e aponta para o ficheiro `docs/fases/FASE-N-*.md`
correspondente. O `AGENTS.md` já instrui qualquer agente a não avançar
de fase sem confirmação, mas repetir isto no início da conversa evita
que o agente assuma o estado errado do projecto.
