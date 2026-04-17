# Workflow: Postmortem Loop

## Quando usar

Após qualquer entrega que causou:
- Bug em dev descoberto após implementação
- Workaround aplicado sem documentação
- Regressão em endpoint existente
- Quebra silenciosa do frontend (envelope errado, CORS ausente)

## Passos

1. **Descrever** — preencher `ai/templates/postmortem.template.md` e salvar em `ai/postmortems/<data>-<feature>.md`
2. **Identificar causa raiz** — não parar no sintoma
3. **Atualizar regras** — se a causa raiz for evitável via regra:
   - Adicionar anti-padrão em `context-pack.md` (via governance)
   - Atualizar checklist do `code-reviewer` agent
4. **Fechar o loop** — confirmar que a regra nova teria evitado o problema

## Exemplos de loop

> Bug: timestamp de update usando `NOW()` — SQLite não reconheceu, salvou NULL.
>
> Causa raiz: desenvolvedor veio do MySQL e não conhecia a limitação do SQLite.
>
> Fix de regra: anti-padrão adicionado em `context-pack.md` — "NUNCA usar `NOW()` — banco é SQLite".

---

> Bug: CORS não configurado em novo controller → frontend recebia erro de preflight.
>
> Causa raiz: checklist de controller não foi seguido.
>
> Fix de regra: anti-padrão reforçado: "CORS configurado no `behaviors()` de CADA controller, nunca global".
