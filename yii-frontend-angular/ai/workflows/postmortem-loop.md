# Workflow: Postmortem Loop

## Quando usar

Após qualquer entrega que causou:
- Bug em produção/dev descoberto após o merge
- Workaround aplicado sem documentação
- Regressão em feature existente

## Passos

1. **Descrever** — preencher `ai/templates/postmortem.template.md` e salvar em `ai/plans/YYYY-MM/postmortem-<feature>.md`
2. **Identificar causa raiz** — não parar no sintoma
3. **Atualizar regras** — se a causa raiz for evitável via regra:
   - Adicionar anti-padrão em `context-pack.md` (via governance)
   - Atualizar checklist do `code-reviewer` agent
4. **Fechar o loop** — confirmar que a regra nova teria evitado o problema

## Exemplo de loop

> Bug: child tab fez chamada de API própria, causando race condition com o resolve do pai.
>
> Causa raiz: desenvolvedor não conhecia o `userEditContext`.
>
> Fix de regra: anti-padrão adicionado em `context-pack.md` — "Child tab não faz chamada de API própria".
>
> Checklist atualizado em `code-reviewer`.
