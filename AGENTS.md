# AGENTS.md — systemPHP

Guia completo em [`docs/05-guia-para-agentes.md`](./docs/05-guia-para-agentes.md).
Lê esse ficheiro antes de gerar ou alterar código neste projecto.

Resumo essencial:

- Laravel + MySQL (Docker) + Sanctum + Pest + Pint + Larastan + Scramble.
  Ver `docs/04-stack-e-ferramentas.md` para o porquê de cada escolha.
- Arquitectura em camadas: Route → Form Request → Controller (fino) →
  Action → Model / API Resource. Ver `docs/03-arquitetura.md`.
- API sempre versionada (`/api/v1/...`). Nome do projecto sempre via
  `config('app.name')`, nunca hardcoded.
- Toda a Action nova precisa de teste Pest. Toda a validação passa por
  um Form Request. Toda a resposta passa por um API Resource.
- Sem emojis em código ou documentação. Comentários e nomes de domínio
  em português; convenções do próprio framework em inglês.
- Trabalho por etapas pequenas e confirmadas — nunca uma reescrita
  grande de uma só vez.
- Estado actual e decisões pendentes: `docs/01-visao-geral.md` e
  `docs/03-arquitetura.md` (secção "Estado das decisões").
