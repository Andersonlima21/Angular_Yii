# Protocolo de Handoff entre Agentes

Este documento define como os agentes passam contexto entre si ao longo de uma sessão ou feature.

## Quando fazer handoff

- Um agente encerrou sua parte e outro precisa continuar
- O escopo da tarefa mudou de domínio (ex: backend → frontend)
- O tech-lead está coordenando múltiplos agentes em paralelo

## O que incluir no handoff

```
## Handoff de [agente-origem] → [agente-destino]

**O que foi feito:**
- [lista do que foi implementado/decidido]

**Estado atual:**
- [arquivos criados/modificados]
- [comandos executados]
- [testes passando/falhando]

**O que falta:**
- [próximas ações para o agente destino]

**Decisões tomadas que impactam você:**
- [qualquer decisão de arquitetura ou contrato relevante]

**Contexto importante:**
- [gotchas, limitações conhecidas, workarounds]
```

## Fluxo típico de uma feature cross-stack

```
tech-lead (coordenação)
  ├── backend-developer (implementação Yii2)
  │     └── handoff → qa-engineer
  ├── ui-ux-designer (implementação AngularJS)
  │     └── handoff → qa-engineer
  └── qa-engineer (validação)
        └── handoff → tech-lead-reviewer
              └── aprovação final
```

## Regras

1. O agente destino não deve assumir nada que não esteja explícito no handoff
2. Se o handoff está incompleto, perguntar antes de prosseguir
3. O tech-lead-reviewer é sempre o último da cadeia — nunca pular
