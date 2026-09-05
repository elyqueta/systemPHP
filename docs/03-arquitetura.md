# Arquitectura — systemPHP

## Estrutura de pastas alvo

```
systemPHP/
├── app/
│   ├── Actions/                  # Lógica de negócio, uma classe por acção
│   │   └── Empresas/
│   │       ├── CriarEmpresaAction.php
│   │       └── AtualizarConfigFiscalAction.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/           # Controllers da API (finos)
│   │   │   └── Admin/            # Controllers do painel Blade
│   │   ├── Requests/             # Form Requests (validação de input)
│   │   │   └── Empresas/
│   │   │       └── CriarEmpresaRequest.php
│   │   └── Resources/            # API Resources (forma da resposta JSON)
│   │       └── EmpresaResource.php
│   ├── Models/                   # Eloquent Models
│   ├── Exceptions/                # Exception Handler customizado
│   └── Providers/
├── config/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   └── views/admin/                # Blade do painel administrativo
├── routes/
│   ├── api.php                     # prefixo /api/v1/...
│   └── web.php                     # painel admin
├── tests/
│   ├── Feature/                     # testes Pest ponta-a-ponta (rota → resposta)
│   └── Unit/                        # testes Pest de Actions isoladas
├── docker-compose.yml
└── .github/workflows/ci.yml
```

## Camadas e responsabilidades

Este é o equivalente directo, em Laravel, ao padrão
`routes → controller → service → repository` usado no projecto
KwanzaFolha Cloud (Node/Express). Os nomes mudam, a filosofia não:

| Camada                       | Responsabilidade                                                                                                                                                 | Equivalente no KwanzaFolha (Node)                                                                          |
| ---------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------- |
| **Route** (`routes/api.php`) | Define o caminho HTTP e qual Controller trata o pedido                                                                                                           | `routes/*.routes.ts`                                                                                       |
| **Form Request**             | Valida e normaliza o input, antes de qualquer lógica correr                                                                                                      | `validators/*.ts` + middleware `validarBody`                                                               |
| **Controller**               | Recebe o pedido já validado, chama a Action certa, devolve a resposta via Resource. Nunca contém lógica de negócio.                                              | `controllers/*.controller.ts` (thin, `asyncHandler`)                                                       |
| **Action**                   | Contém a lógica de negócio de uma única operação (ex: "criar empresa"). Testável isoladamente, sem HTTP.                                                         | `services/*.service.ts`                                                                                    |
| **Model (Eloquent)**         | Representa uma tabela, define relações, mas **não** deve conter regras de negócio complexas — só o que é intrínseco aos dados (casts, relações, scopes simples). | `repositories/*.repository.ts` (mas o Eloquent já embute grande parte do que era SQL cru)                  |
| **API Resource**             | Define o formato exacto da resposta JSON — desacopla o schema da base de dados do contrato público da API.                                                       | Ausente no KwanzaFolha (era `res.json(row)` directo — um dos pontos identificados para melhorar lá também) |
| **Exception Handler**        | Centraliza a tradução de excepções em respostas HTTP consistentes.                                                                                               | `middlewares/error.middleware.ts` + `errors/AppError.ts`                                                   |

### Porquê "Controllers finos"

Um erro comum em projectos Laravel iniciantes é colocar toda a lógica
dentro do Controller. Isto funciona a curto prazo, mas cria dois
problemas à medida que o produto cresce:

1. **Impossível de testar isoladamente** — testar a lógica de negócio
   obriga sempre a simular um pedido HTTP completo.
2. **Reutilização difícil** — se amanhã a mesma lógica for precisa a
   partir de um comando `artisan` (ex: processar folha de pagamento à
   meia-noite via `schedule`), duplicar-se-ia o código do Controller.

Com Actions, o Controller fica assim (exemplo ilustrativo):

```php
public function store(CriarEmpresaRequest $request, CriarEmpresaAction $action)
{
    $empresa = $action->execute($request->validated());

    return new EmpresaResource($empresa);
}
```

Toda a lógica de "como criar uma empresa" vive em
`CriarEmpresaAction::execute()`, testável sem qualquer pedido HTTP.

### Versionamento da API desde o início

Todas as rotas de API vivem sob `/api/v1/...`, nunca `/api/...` sem
versão. Um produto real acumula clientes (apps, integrações) que não
podem ser todos actualizados ao mesmo tempo. Introduzir versionamento
depois de já existirem consumidores é muito mais doloroso do que
começar por ele.

### Tratamento de erros e formato de resposta

Seguindo o mesmo princípio já adoptado no KwanzaFolha (mas ainda por
implementar lá): respostas de erro consistentes, com um código
"machine-readable" e uma mensagem legível, por exemplo:

```json
{
    "error": {
        "code": "EMPRESA_NAO_ENCONTRADA",
        "message": "Empresa não encontrada."
    }
}
```

Isto é centralizado no Exception Handler (`app/Exceptions/Handler.php`
customizado), nunca decidido individualmente em cada Controller.

## Painel administrativo (Blade)

O painel administrativo é servido pelo mesmo Laravel, usando Blade
(motor de templates nativo), sob `routes/web.php`. Vive lado a lado com
a API, mas com Controllers e autenticação próprios — a API usa Sanctum
com tokens, o painel usa sessões (o padrão do Laravel para aplicações
web tradicionais).

Esta separação Controllers `Api/V1/` vs `Admin/` evita misturar as duas
preocupações (uma serve JSON para clientes externos, a outra serve HTML
para um utilizador humano autenticado por sessão).

## Testes

Todo o Action nova ganha, no mínimo:

- Um teste de **Feature** (Pest) que simula o pedido HTTP completo e
  confirma a resposta.
- Um teste de **Unit** (Pest) que chama a Action directamente, sem HTTP,
  cobrindo casos de borda da lógica de negócio.

Nenhuma funcionalidade se considera "concluída" sem, pelo menos, o teste
de Feature correspondente.

## Estado das decisões

| Decisão                      | Estado                                                             |
| ---------------------------- | ------------------------------------------------------------------ |
| Só API vs API + painel admin | **Fechado**: API + painel admin em Blade                           |
| Framework de testes          | **Fechado**: Pest                                                  |
| Autenticação da API          | **Fechado**: Laravel Sanctum                                       |
| Base de dados                | **Fechado**: MySQL 8.4 via Docker                                  |
| Domínio de negócio           | **Pendente** — ver `01-visao-geral.md`                             |
| CI (GitHub Actions)          | **Pendente** — a configurar depois da base do projecto estar de pé |
