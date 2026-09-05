# systemPHP

Aplicação em **Laravel** (API + painel administrativo), construída como
projecto de aprendizagem prática de PHP/Laravel e, ao mesmo tempo, como
produto profissional — não um tutorial descartável.

O nome exibido da aplicação **não está fixo no código**. É lido a partir da
variável de ambiente `APP_NAME` (ficheiro `.env`) através de
`config('app.name')`, em qualquer parte do projecto. Mudar o nome nunca
implica mudar lógica.

---

## Visão rápida

| Camada                | Tecnologia                                   |
| --------------------- | -------------------------------------------- |
| Linguagem             | PHP 8.3                                      |
| Framework             | Laravel (última LTS)                         |
| Base de dados         | MySQL 8.4 (via Docker)                       |
| Autenticação da API   | Laravel Sanctum                              |
| Testes                | Pest                                         |
| Formatação de código  | Laravel Pint                                 |
| Análise estática      | Larastan (PHPStan para Laravel)              |
| Documentação da API   | Scramble (OpenAPI gerado a partir do código) |
| Painel administrativo | Blade (views nativas do Laravel)             |

---

## Documentação completa

Toda a visão do projecto, arquitectura, guia de instalação e convenções
estão na pasta [`docs/`](./docs). Começa por aqui:

1. [`docs/01-visao-geral.md`](./docs/01-visao-geral.md) — o que é este
   projecto, para quem é, e porquê estas escolhas.
2. [`docs/02-instalacao.md`](./docs/02-instalacao.md) — guia passo a passo
   de instalação (ambiente, PHP, Composer, Laravel, Docker/MySQL).
3. [`docs/03-arquitetura.md`](./docs/03-arquitetura.md) — estrutura de
   pastas, camadas da aplicação, e o porquê de cada decisão.
4. [`docs/04-stack-e-ferramentas.md`](./docs/04-stack-e-ferramentas.md) —
   cada ferramenta instalada: para que serve, porque foi escolhida, e que
   vantagem traz.
5. [`docs/05-guia-para-agentes.md`](./docs/05-guia-para-agentes.md) —
   convenções e regras do projecto, pensadas para orientar tanto pessoas
   como agentes de IA (Claude, Copilot, etc.) a produzirem código
   consistente com a arquitectura escolhida.

Também existe um [`AGENTS.md`](./AGENTS.md) na raiz — um resumo curto que
qualquer ferramenta de IA lê automaticamente, apontando para o guia
completo em `docs/05-guia-para-agentes.md`.

---

## Arranque rápido (depois de instalado — ver `docs/02-instalacao.md`)

```bash
# subir a base de dados
docker compose up -d

# instalar dependências PHP
composer install

# copiar variáveis de ambiente e gerar a chave da aplicação
cp .env.example .env
php artisan key:generate

# correr migrations
php artisan migrate

# arrancar o servidor de desenvolvimento
php artisan serve
```

Aplicação disponível em `http://localhost:8000`.

---

## Estado do projecto

Em construção, guiado passo a passo. O progresso e as decisões tomadas em
cada etapa estão registados em `docs/03-arquitetura.md`.
