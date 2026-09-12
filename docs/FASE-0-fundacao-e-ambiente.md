# Fase 0 — Fundação e Ambiente

Objectivo: preparar o repositório para receber o domínio de negócio em
PostgreSQL, sem ainda escrever nenhuma migration de negócio. No fim
desta fase, `php artisan migrate` deve correr contra PostgreSQL sem
erros, mesmo que só com as tabelas base do Laravel.

## 1. Trocar Docker de MySQL para PostgreSQL

Substituir o conteúdo de `docker-compose.yml` (raiz do projeto) por:

```yaml
services:
  db:
    image: postgres:16-alpine
    container_name: systemphp_db
    restart: unless-stopped
    environment:
      POSTGRES_DB: systemphp
      POSTGRES_USER: systemphp_app
      POSTGRES_PASSWORD: trocaesta
    ports:
      - "127.0.0.1:5435:5432"
    volumes:
      - systemphp_db_data:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U systemphp_app"]
      interval: 5s
      timeout: 5s
      retries: 5

volumes:
  systemphp_db_data:
```

Nota sobre a porta: usa-se `5435` (não `5432` nem `5434`) porque o
KwanzaFolha Cloud já ocupa a `5434` na mesma máquina de desenvolvimento
do Ely. Confirmar com `docker compose ps` de todos os projectos activos
se houver dúvida.

Se já existir um volume Docker antigo chamado `systemphp_db_data` (do
MySQL anterior), ele **não é reaproveitável** — dados de MySQL não
correm num container Postgres. Remover explicitamente:

```bash
docker compose down -v
```

O `-v` remove os volumes. Só correr isto se tiveres a certeza de que
não há dados a perder no MySQL antigo.

## 2. Extensão PHP para PostgreSQL

Adicionar ao guia de instalação (`docs/02-instalacao.md`, secção 1) a
extensão que falta:

```bash
sudo apt install -y php8.3-pgsql
```

Verificar:

```bash
php -m | grep pgsql
```

Deve listar `pgsql` e `pdo_pgsql`.

## 3. Actualizar `.env`

Copiar `.env.example` para `.env` (se ainda não existir) e ajustar:

```env
APP_NAME=SYSTEM
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5435
DB_DATABASE=systemphp
DB_USERNAME=systemphp_app
DB_PASSWORD=trocaesta
```

Gerar a chave da aplicação, se ainda não foi feito:

```bash
php artisan key:generate
```

## 4. Instalar as dependências em falta

Confirmado por inspecção do `composer.json`: `laravel/sanctum` e
`dedoc/scramble` **não estão instalados**, apesar de `docs/04` os listar
como decisão fechada. Instalar agora:

```bash
composer require laravel/sanctum
composer require dedoc/scramble
composer require spatie/laravel-permission
composer require --dev larastan/larastan
```

Publicar a configuração do Sanctum e do Spatie Permission:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Isto cria migrations novas em `database/migrations/` (tokens do
Sanctum, tabelas `roles`/`permissions` do Spatie). Não editar essas
migrations publicadas — são geridas pelos próprios pacotes.

Nota: **não é preciso nenhum pacote extra para autenticação de
utilizadores** — a tabela `users` nativa do Laravel é reaproveitada
directamente (ver `docs/00-decisoes-revisadas.md`, secção 6). A Fase 1
só lhe acrescenta colunas.

## 5. Criar `routes/api.php`

Este ficheiro ainda não existe no projecto. Criar com o conteúdo
mínimo:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // As rotas de cada módulo entram aqui, a partir da Fase 3.
});
```

E registar no `bootstrap/app.php`, dentro de `withRouting`:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

## 6. Criar a estrutura de pastas em camadas

Ainda não existem. Criar (vazias, ou com um `.gitkeep`) para deixar a
intenção explícita:

```
app/Actions/
app/Http/Requests/
app/Http/Resources/
app/Traits/
```

O `app/Traits/HasUuid.php` é criado já na Fase 1.

## Checkpoint

Antes de avançar para a Fase 1, correr e colar o resultado destes
comandos (ver `HUMAN.md`, secção "Fase 0"):

```bash
php -v
composer -V
docker compose up -d
docker compose ps
php -m | grep pgsql
php artisan migrate:status
```

`php artisan migrate:status` deve mostrar as migrations base do Laravel
(`users`, `cache`, `jobs`) mais as novas do Sanctum e do Spatie
Permission.
