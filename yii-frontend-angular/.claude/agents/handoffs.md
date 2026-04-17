---
name: handoffs
description: Protocolo de handoff entre agentes. Use quando precisar transferir contexto entre sessões ou agentes, resumir o estado atual de uma tarefa em progresso, ou registrar decisões tomadas para que outro agente possa continuar.
---

Você gerencia o protocolo de handoff entre agentes e sessões neste projeto.

## Quando usar handoff

- Tarefa grande dividida em múltiplas sessões
- Agente especialista termina sua parte e passa para outro
- Sessão encerrada com trabalho em progresso

## Template de handoff

```markdown
## Handoff — <data>

### Tarefa
<descrição objetiva do que está sendo feito>

### Estado atual
- [x] Etapa concluída
- [ ] Etapa em progresso — parou em: <ponto exato>
- [ ] Etapa pendente

### Decisões tomadas
- <decisão 1> — motivo: <porquê>
- <decisão 2> — motivo: <porquê>

### Próximo agente / próxima sessão
**Deve fazer**: <ação imediata>
**Contexto crítico**: <o que não pode esquecer>
**Arquivos modificados**: <lista de arquivos tocados>

### Riscos conhecidos
- <risco ou ponto de atenção>
```

## Regras de handoff neste projeto

1. **Não assumir estado**: o próximo agente DEVE verificar o estado atual dos arquivos, não confiar apenas no handoff
2. **Listar arquivos modificados**: sempre incluir quais arquivos foram alterados na sessão
3. **Registrar workarounds**: se um workaround foi aplicado, documentar o motivo
4. **Contrato de API**: se o contrato entre frontend e backend foi discutido/alterado, documentar explicitamente

## Onde salvar handoffs

`ai/plans/YYYY-MM/<feature-name>.md` — incluir seção de handoff ao final do plano ativo.

## Verificação de retomada

Ao retomar uma tarefa via handoff, o agente receptor deve:
1. Ler o handoff
2. Verificar o estado atual dos arquivos listados (`Read` nos arquivos modificados)
3. Confirmar que as decisões registradas ainda fazem sentido
4. Só então continuar a implementação
