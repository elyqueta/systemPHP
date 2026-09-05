# Visão Geral — systemPHP

## O que é este projecto

O **systemPHP** é uma aplicação Laravel com duas faces:

1. **API REST** (`/api/v1/...`) — consumível por qualquer cliente externo
   (frontend separado, app móvel, integrações).
2. **Painel administrativo** (Blade, servido pelo próprio Laravel) — para
   gestão interna, sem depender de um frontend separado.

Este projecto tem **dois objectivos simultâneos**, e é importante que
ambos guiem as decisões técnicas:

- **Aprendizagem**: é o veículo prático para aprender PHP e Laravel a
  sério, praticando (não só lendo tutoriais).
- **Produto**: não é descartável. É construído com o nível de rigor de
  algo que vai para produção — testes, segurança, documentação,
  arquitectura pensada para crescer.

Estes dois objectivos não entram em conflito: a melhor forma de aprender
um framework a fundo é construir algo real com boas práticas desde o
início, em vez de aprender "o básico" e só depois tentar tornar
profissional (isso normalmente significa reescrever tudo).

## Contexto e motivação

Este projecto nasce de uma decisão de trocar a stack principal de
aprendizagem de **Node.js/TypeScript/Express** (usada num outro projecto,
o _KwanzaFolha Cloud_, um ERP de RH e Folha de Salários para Angola) para
**PHP/Laravel**, motivada por:

- Custo de hospedagem — hosting PHP + MySQL é amplamente disponível a
  baixo custo (cPanel, hosting partilhado), ao contrário de stacks
  Node.js ou bases de dados como Oracle, que exigem infraestrutura mais
  cara ou especializada.
- Usabilidade e procura no mercado — PHP/Laravel continua a ser uma das
  stacks mais usadas no mundo para aplicações web e é amplamente
  ensinada em ambientes de estágio.

O domínio de negócio (que tipo de dados o sistema vai gerir) ainda **não
está fechado**. Duas opções em cima da mesa:

- Reaproveitar o domínio já dominado (RH/Folha de Salários, INSS, IRT,
  Kwanza, empresas multi-tenant) — vantagem: já se conhecem as regras de
  negócio, o esforço fica todo em aprender o framework.
- Um domínio novo, mais simples, para não misturar "aprender Laravel"
  com "recordar regras fiscais complexas" ao mesmo tempo.

Esta decisão fica registada aqui como **pendente** — deve ser actualizada
assim que for tomada, para que qualquer pessoa (ou agente de IA) que
consulte este documento saiba o estado actual.

## Princípios que guiam este projecto

1. **Convenção sobre configuração** — seguir as convenções do Laravel
   sempre que possível, em vez de reinventar padrões que o framework já
   resolve (nomes de tabelas, migrations, injecção de dependências,
   etc.). Isto é uma mudança deliberada de mentalidade vinda do Express,
   onde quase tudo era construído manualmente.
2. **Controllers finos** — controllers só orquestram (recebem o pedido,
   chamam a lógica, devolvem a resposta). Lógica de negócio vive em
   `Actions`.
3. **Nunca confiar em input sem validar** — todo o input externo passa
   por um Form Request dedicado antes de chegar à lógica de negócio.
4. **Testes não são opcionais** — qualquer funcionalidade nova (Action,
   endpoint) ganha pelo menos um teste de Feature em Pest antes de se
   considerar "concluída".
5. **Trabalho incremental e confirmado** — cada etapa é pequena, testada,
   e confirmada antes de avançar para a seguinte. Nunca uma reescrita
   grande de uma só vez.
6. **Documentação viva** — esta pasta `docs/` deve reflectir sempre o
   estado real do projecto. Decisões tomadas entram aqui, não ficam só
   na conversa.

## Relação com o projecto KwanzaFolha Cloud (Node/TypeScript)

O systemPHP **não é uma migração** do KwanzaFolha Cloud. É um projecto
novo e independente, com o seu próprio ciclo de vida, arquitectura e
código. Nenhuma linha de código é partilhada entre os dois. O que pode
ser reaproveitado, se o domínio escolhido for o mesmo, é o conhecimento
das regras de negócio — não a implementação.

Os dois projectos podem coexistir e evoluir em paralelo, sem que um
dependa do outro.
