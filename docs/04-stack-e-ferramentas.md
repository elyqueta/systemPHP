# Stack e Ferramentas — systemPHP

Para cada ferramenta: o que é, para que serve, porque foi escolhida em
vez de alternativas, e que vantagem concreta traz ao objectivo de
"produto profissional".

---

## Laravel

**O que é**: framework PHP full-stack (API + web + filas + agendamento
de tarefas, tudo incluído).

**Porquê**: é o framework PHP mais usado e mais empregável no mercado
actual. Resolve de fábrica problemas que, no Express, tiveram de ser
montados manualmente (routing, injecção de dependências, ORM, validação,
autenticação, filas, agendamento).

**Vantagem concreta**: menos código "de infraestrutura" escrito à mão,
mais tempo focado em lógica de negócio — e, por seguir convenções muito
estabelecidas, qualquer outro developer Laravel entende a estrutura do
projecto sem explicação.

---

## MySQL 8.4

**O que é**: sistema de gestão de base de dados relacional.

**Porquê MySQL e não PostgreSQL** (usado no KwanzaFolha): o critério
aqui é hospedagem barata e ampla compatibilidade — a esmagadora maioria
dos serviços de hosting PHP económicos (cPanel, hosting partilhado)
oferece MySQL/MariaDB por defeito. O Eloquent (ORM do Laravel) também é
optimizado e documentado sobretudo com MySQL como referência.

**Porquê não Oracle**: licenciamento caro, praticamente ausente em
hosting barato, e sem vantagem técnica relevante para este projecto.

---

## Laravel Sanctum

**O que é**: pacote oficial do Laravel para autenticação de APIs via
tokens, e também de aplicações SPA via cookies.

**Porquê e não Passport**: Passport implementa OAuth2 completo — útil
quando se precisa de emitir tokens para aplicações de terceiros (ex:
"iniciar sessão com a tua conta systemPHP" noutro site). Não é o caso
aqui. Sanctum é mais simples, mais leve, e cobre exactamente o caso de
uso: uma API própria autenticada por token, consumida por um cliente
próprio (o painel Blade, ou uma app futura).

**Vantagem concreta**: menos superfície de configuração e ataque do que
um OAuth2 completo desnecessário.

---

## Pest

**O que é**: framework de testes para PHP, construído sobre o PHPUnit,
com sintaxe mais legível e moderna.

**Porquê e não PHPUnit puro**: Pest é hoje o standard de facto em
projectos Laravel modernos, mantido pela mesma equipa próxima do
ecossistema Laravel. A sintaxe é mais próxima de frameworks de teste
modernos (tipo Jest, familiar de quem já testou JavaScript).

**Exemplo de sintaxe**:

```php
it('cria uma empresa com dados válidos', function () {
    $response = $this->postJson('/api/v1/empresas', [
        'nome' => 'Kwanza Tech Lda',
    ]);

    $response->assertCreated();
});
```

**Vantagem concreta**: testes mais rápidos de escrever e ler, o que
reduz a fricção de "testar tudo desde o início" — um dos princípios
deste projecto.

---

## Laravel Pint

**O que é**: formatador de código oficial do Laravel (construído sobre
o PHP-CS-Fixer), com as convenções de estilo do próprio framework
pré-configuradas.

**Equivalente directo**: o Prettier do projecto KwanzaFolha.

**Vantagem concreta**: zero discussão sobre estilo de código — corre-se
`./vendor/bin/pint` e o código fica formatado de acordo com o standard
Laravel, sem configuração manual.

---

## Larastan (PHPStan para Laravel)

**O que é**: ferramenta de análise estática — encontra erros de tipo,
chamadas a métodos inexistentes, e código morto, sem executar o código.

**Equivalente directo**: o `strict mode` do TypeScript + ESLint no
KwanzaFolha.

**Vantagem concreta**: apanha erros antes de correrem em produção (ou
até antes de correr os testes), especialmente relevante em PHP, que é
uma linguagem de tipagem mais fraca que TypeScript por natureza.

---

## Scramble

**O que é**: gerador automático de documentação OpenAPI (Swagger) a
partir do próprio código — lê os Form Requests, Resources e rotas, e
produz a documentação sem precisar de anotações manuais espalhadas pelo
código (ao contrário do `swagger-jsdoc`, usado no KwanzaFolha, que exige
comentários `@openapi` manuais em cada rota).

**Vantagem concreta**: a documentação nunca fica desactualizada em
relação ao código, porque é derivada dele automaticamente.

---

## Docker (só para a base de dados, por agora)

**O que é**: containerização — corre o MySQL isolado do sistema
operativo da máquina.

**Porquê só a BD e não a aplicação toda em Docker (ainda)**: para
aprendizagem, correr o PHP directamente na máquina com `php artisan
serve` dá feedback mais rápido e mensagens de erro mais directas.
Containerizar a aplicação inteira (via Laravel Sail, por exemplo) é um
passo a considerar mais tarde, quando o foco passar de "aprender" para
"preparar para deploy".

**Vantagem concreta**: ambiente de base de dados reprodutível, igual em
qualquer máquina, sem "sujar" o sistema operativo com instalações
directas de MySQL.

---

## GitHub Actions (CI) — planeado, ainda não implementado

**O que vai ser**: um workflow que corre automaticamente, em cada push,
os testes Pest, o Pint (verificação de formatação) e o Larastan
(análise estática).

**Vantagem concreta**: garante que nenhum código que falhe testes ou
quebre convenções de estilo chega à branch principal, sem depender de
disciplina manual.

---

## Resumo de correspondência com o projecto KwanzaFolha (Node/TypeScript)

| Preocupação            | KwanzaFolha (Node)                       | systemPHP (Laravel)   |
| ---------------------- | ---------------------------------------- | --------------------- |
| Validação de input     | Zod                                      | Form Requests         |
| Tipagem estrita        | TypeScript strict                        | Larastan              |
| Formatação             | Prettier                                 | Pint                  |
| Documentação da API    | swagger-jsdoc (manual)                   | Scramble (automático) |
| Autenticação           | JWT manual (`jsonwebtoken` + `bcryptjs`) | Laravel Sanctum       |
| Acesso à base de dados | `pg` com SQL cru                         | Eloquent ORM          |
| Lógica de negócio      | Services                                 | Actions               |
| Testes                 | Ainda não implementado                   | Pest, desde o início  |
