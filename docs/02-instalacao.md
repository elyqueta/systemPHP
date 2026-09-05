# Guia de Instalação — systemPHP

Ambiente alvo: **Linux (Ubuntu/Debian)**. Todos os comandos abaixo foram
testados neste contexto.

Cada secção explica: o que se instala, para que serve, e que vantagem
traz para o objectivo de construir "algo profissional".

---

## 1. PHP 8.3 e extensões

### O que é e para que serve

PHP é a linguagem de execução do Laravel. As "extensões" são módulos que
dão a PHP capacidades extra (falar com MySQL, processar texto em UTF-8,
XML, ficheiros comprimidos, etc.) que o Laravel exige para funcionar
correctamente.

### Porquê a versão 8.3 especificamente

O Laravel moderno (12.x) exige, no mínimo, PHP 8.2. A versão 8.3 traz
melhorias de performance e tipagem mais rigorosa (readonly properties,
melhor inferência de tipos), o que ajuda a escrever código mais seguro —
o equivalente, em espírito, ao `strict mode` do TypeScript.

### Instalar o Composer

O repositório padrão do Ubuntu costuma trazer versões desactualizadas de
PHP. Usa-se o PPA do Ondřej Surý, que é o standard de facto na comunidade
para ter versões recentes de PHP em Ubuntu/Debian:

```bash
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

```bash
sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-mysql \
  php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath \
  php8.3-gd php8.3-intl unzip curl git
```

O que cada extensão faz, resumidamente:

| Extensão          | Para quê                                                                |
| ----------------- | ----------------------------------------------------------------------- |
| `php8.3-mysql`    | Ligação do PHP à base de dados MySQL                                    |
| `php8.3-mbstring` | Manipulação segura de strings multi-byte (acentuação, UTF-8)            |
| `php8.3-xml`      | Leitura/escrita de XML (usado por várias dependências internas)         |
| `php8.3-curl`     | Pedidos HTTP a partir do PHP (chamadas a APIs externas)                 |
| `php8.3-zip`      | Composer usa isto para instalar pacotes                                 |
| `php8.3-bcmath`   | Matemática de precisão arbitrária (útil se houver cálculos financeiros) |
| `php8.3-gd`       | Processamento de imagens                                                |
| `php8.3-intl`     | Formatação de números, datas e moedas por localidade                    |

### Verificação

```bash
php -v
```

Esperado: `PHP 8.3.x`.

---

## 2. Composer

### O que é o Composer e para que serve

Composer é o gestor de dependências do PHP — o equivalente directo ao
`npm` no Node.js. Instala e gere bibliotecas de terceiros (incluindo o
próprio Laravel), a partir de um ficheiro `composer.json` (equivalente ao
`package.json`).

### Vantagem

Sem o Composer, seria preciso descarregar e gerir manualmente cada
biblioteca PHP usada no projecto. Com ele, `composer install` recria o
ambiente exacto de dependências em qualquer máquina — essencial para um
produto que outras pessoas (ou um servidor de produção) vão correr.

### Instalação

```bash
curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
```

### Verificação do Composer

```bash
composer --version
```

---

## 3. Criar o projecto Laravel

### O que acontece

O Composer descarrega o esqueleto do Laravel (framework, estrutura de
pastas, ficheiros de configuração) e cria um projecto novo chamado
`systemPHP`.

```bash
cd ~/Documentos/projectos   # ou onde preferires guardar os teus projectos
composer create-project laravel/laravel systemPHP
cd systemPHP
```

### Estrutura gerada (resumo)

```text
systemPHP/
├── app/          # código da aplicação (Models, Controllers, etc.)
├── bootstrap/     # arranque interno do framework
├── config/        # ficheiros de configuração (lêem o .env)
├── database/      # migrations, seeders, factories
├── public/        # ponto de entrada HTTP (index.php)
├── resources/     # views (Blade), assets
├── routes/        # definição de rotas (web.php, api.php)
├── .env           # variáveis de ambiente (nunca vai para o Git)
└── artisan        # CLI do Laravel (equivalente a scripts npm)
```

---

## 4. Nome do projecto configurável (`APP_NAME`)

O Laravel já resolve isto de fábrica — não é preciso construir nada.

No `.env`, garante:

```env
APP_NAME="systemPHP"
```

Em qualquer parte do código, o nome é lido via:

```php
config('app.name')
```

Nunca escrever o nome do projecto directamente ("hardcoded") em código,
views ou mensagens — usar sempre `config('app.name')`. Mudar o nome do
produto no futuro implica mudar uma única linha no `.env`.

---

## 5. MySQL via Docker

### Porquê Docker e não instalar o MySQL directamente na máquina

Mantém o ambiente de desenvolvimento isolado, reprodutível, e igual ao
que qualquer outro colaborador (ou o próprio ambiente de CI) vai usar.
Evita o problema clássico de "funciona na minha máquina" causado por
versões diferentes de MySQL instaladas localmente por diferentes
projectos.

### `docker-compose.yml`

Criar na raiz do projecto (`systemPHP/docker-compose.yml`):

```yaml
services:
    db:
        image: mysql:8.4
        container_name: systemPHP_db
        restart: unless-stopped
        environment:
            MYSQL_DATABASE: systemPHP
            MYSQL_USER: systemPHP_app
            MYSQL_PASSWORD: trocaesta
            MYSQL_ROOT_PASSWORD: trocaestatambem
        ports:
            - "127.0.0.1:3307:3306"
        volumes:
            - systemPHP_db_data:/var/lib/mysql
        healthcheck:
            test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
            interval: 5s
            timeout: 5s
            retries: 5

volumes:
    systemPHP_db_data:
```

Nota: a porta externa é `3307` (não `3306`) para evitar colisão caso já
exista outro MySQL local ou em Docker a usar a porta padrão.

Antes de usar em produção, **trocar as passwords** `trocaesta` e
`trocaestatambem` por valores fortes e nunca as comitar no Git.

### Subir o container

```bash
docker compose up -d
docker compose ps
```

Esperar o estado `healthy`.

---

## 6. Ligar o Laravel ao MySQL

No `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=systemPHP
DB_USERNAME=systemPHP_app
DB_PASSWORD=trocaesta
```

Gerar a chave de aplicação (usada para encriptação interna — sessões,
cookies assinados):

```bash
php artisan key:generate
```

---

## 7. Testar tudo

```bash
php artisan serve
```

Abrir `http://localhost:8000` — deve mostrar a página de boas-vindas do
Laravel, ou "systemPHP" se a rota de teste em `routes/web.php` já tiver
sido ajustada para devolver `config('app.name')`.

---

## Resolução de problemas comuns

| Sintoma                                         | Causa provável                                                 | Solução                                                            |
| ----------------------------------------------- | -------------------------------------------------------------- | ------------------------------------------------------------------ |
| `composer: command not found`                   | Composer não está em `/usr/local/bin`                          | Repetir passo 2, confirmar `echo $PATH` inclui `/usr/local/bin`    |
| Erro de ligação à BD (`SQLSTATE[HY000] [2002]`) | Container Docker não está `healthy`, ou porta errada no `.env` | `docker compose ps`, confirmar porta `3307` no `.env`              |
| `php artisan key:generate` falha                | `.env` não existe                                              | `cp .env.example .env` antes de gerar a chave                      |
| Extensão em falta ao correr `composer install`  | Faltou instalar alguma extensão PHP do passo 1                 | A mensagem de erro do Composer diz exactamente qual extensão falta |
