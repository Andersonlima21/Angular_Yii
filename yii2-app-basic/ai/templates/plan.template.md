# Plano: [Nome da Feature]

**Data**: YYYY-MM-DD
**Agente responsável**: [tech-lead / backend-developer / etc.]
**Status**: [ ] Planning | [ ] Execution | [ ] Verification | [ ] Done

---

## Objetivo

Uma frase descrevendo o que esta feature entrega.

## Impacto por camada

| Camada | O que muda |
|---|---|
| Controller | ex.: nova action, CORS em novo controller |
| Service | ex.: novo método Query Builder + espelho AR |
| Model | ex.: nova regra de validação, novo campo |
| Migration | ex.: sim / não — descrever se sim |
| Contrato de API | ex.: novo endpoint, mudança de shape — comunicar ao frontend |

## Critérios de aceite

- [ ] ...
- [ ] ...

---

## Fases

### Planning
- [ ] Requisitos levantados
- [ ] Impacto em backend e frontend mapeado
- [ ] Contrato de API definido (atualizar `ai/docs/` se necessário)
- [ ] Migration planejada se houver mudança de schema

### Execution
- [ ] Migration criada e aplicada
- [ ] Service implementado (Query Builder + transação)
- [ ] Controller implementado (CORS + validação + envelope)
- [ ] Rota registrada em `config/web.php` se action não-REST
- [ ] `vendor/bin/codecept run` passando

### Verification
- [ ] Testado manualmente via curl ou frontend
- [ ] Envelope correto: `{ success, type, data }`
- [ ] Status codes corretos
- [ ] CORS headers presentes
- [ ] Sem regressões nos endpoints existentes

### Post-delivery
- [ ] `ai/docs/` atualizado se endpoint foi criado/alterado
- [ ] Frontend notificado se contrato mudou
- [ ] Workarounds resolvidos removidos de `context-pack.md` (via governance)
- [ ] Postmortem criado se houve incidente

---

## Handoff (preencher ao pausar)

**Parou em**: [descrição exata do ponto de parada]
**Próxima ação**: [primeira coisa a fazer ao retomar]
**Arquivos modificados**: [lista]
**Decisões tomadas**: [lista com motivos]
