# Workflow: Feature Delivery

## Fases

### 1. Planning
- Criar plano em `ai/plans/YYYY-MM/<feature>.md` a partir de `templates/plan.template.md`
- Mapear impacto em frontend e backend
- Definir contrato de API; se endpoint novo → criar `ai/docs/<recurso>.md`
- Agente: `tech-lead`

### 2. Execution
- Implementar backend primeiro se houver endpoint novo
- Implementar frontend: service → controller/component → view → registrar em `index.html` e `app.js`
- Agente: `frontend-developer` + `backend-integration`
- Verificar: `node --check app/**/*.js`

### 3. Verification
- Testar fluxo principal no browser (backend rodando)
- Testar edge cases levantados pelo `qa-engineer`
- Verificar que fluxos existentes não regrediram

### 4. Post-delivery
- Atualizar `ai/docs/` se contrato mudou
- Remover workaround de `context-pack.md` se foi resolvido (governance)
- Criar postmortem se houve incidente (`templates/postmortem.template.md`)
- Marcar plano como Done

## Checklist rápido

- [ ] Plano criado em `ai/plans/`
- [ ] `ai/docs/` atualizado se endpoint novo/alterado
- [ ] `index.html` atualizado com novos scripts
- [ ] `app.js` atualizado se novo estado
- [ ] `node --check app/**/*.js` limpo
- [ ] Testado no browser com backend rodando
