# Workflow: Feature Delivery

## Fases

### 1. Planning

- Criar plano em `ai/plans/YYYY-MM/<feature>.md` a partir de `templates/plan.template.md`
- Mapear impacto em backend e frontend consumidor
- Se endpoint novo → criar `ai/docs/<recurso>.md` e comunicar ao frontend
- Agente: `tech-lead`

### 2. Execution

- Implementar migration se houver mudança de schema
- Aplicar migration: `php yii migrate`
- Implementar no service (Query Builder primário)
- Implementar no controller (validação + envelope)
- Registrar rota em `config/web.php` se action não-REST
- Agentes: `backend-developer`, `database-architect`

### 3. Verification

- Rodar `vendor/bin/codecept run`
- Testar endpoints manualmente via curl ou frontend
- Verificar envelope de resposta está correto
- Verificar CORS para o origin do frontend (`localhost:5500`)
- Agente: `qa-engineer`

### 4. Post-delivery

- Atualizar `ai/docs/` se o contrato do endpoint mudou
- Remover workaround de `context-pack.md` se foi resolvido (via governance)
- Criar postmortem se houve incidente
- Marcar plano como Done
- Atualizar `standards/changelog.md` se mudou regra congelada

## Checklist rápido

- [ ] Plano criado em `ai/plans/`
- [ ] Migration criada e aplicada (`php yii migrate`)
- [ ] Service com Query Builder + transação
- [ ] Controller com CORS, validação e envelope
- [ ] `ai/docs/` atualizado se endpoint novo/alterado
- [ ] `vendor/bin/codecept run` passando
- [ ] Frontend notificado se o contrato mudou
