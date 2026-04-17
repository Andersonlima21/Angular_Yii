# Catálogo de Agentes

Mapa rápido de task → agente para este projeto Yii2/AngularJS.

## Agentes disponíveis

| Agente | Arquivo | Quando usar |
|---|---|---|
| `backend-developer` | `backend-developer.md` | Controllers, Services, Models, migrations, Yii2/PHP |
| `api-designer` | `api-designer.md` | Contratos REST, endpoints, envelopes, status codes |
| `database-architect` | `database-architect.md` | Schema SQLite, migrations, relacionamentos, model rules |
| `qa-engineer` | `qa-engineer.md` | Testes Codeception, validação de contrato API/frontend |
| `code-reviewer` | `code-reviewer.md` | Revisão de código contra padrões do projeto |
| `tech-lead` | `tech-lead.md` | Coordenação cross-stack, decisões arquiteturais |
| `tech-lead-reviewer` | `tech-lead-reviewer.md` | Gate final de aprovação antes de feature "entregue" |
| `solutions-architect` | `solutions-architect.md` | Arquitetura de plataforma, trade-offs, fases |
| `platform-sre` | `platform-sre.md` | Problemas de ambiente, porta, servidor, Docker |
| `cli-skill-engineer` | `cli-skill-engineer.md` | Governança de ai/, agents, skills, MCP |
| `ui-ux-designer` | `ui-ux-designer.md` | UI/UX AngularJS, protótipos, fluxos de tela |
| `wms-domain-expert` | `wms-domain-expert.md` | Vocabulário de domínio, schema, regras de negócio |
| `wms-operations-analyst` | `wms-operations-analyst.md` | Fluxos de uso, linguagem de UI, priorização |

## Roteamento por tarefa

| Tarefa | Agente primário | Agente de suporte |
|---|---|---|
| Adicionar endpoint novo | `backend-developer` | `api-designer` |
| Criar migration | `database-architect` | `backend-developer` |
| Nova tela no frontend | `ui-ux-designer` | `tech-lead` |
| Feature cross-stack | `tech-lead` | `backend-developer` + `ui-ux-designer` |
| Revisar implementação | `code-reviewer` | `qa-engineer` |
| Aprovar feature | `tech-lead-reviewer` | — |
| Debug de ambiente | `platform-sre` | — |
| Dúvida de domínio | `wms-domain-expert` | — |
| Fluxo de usuário | `wms-operations-analyst` | `ui-ux-designer` |
| Governança de ai/ | `cli-skill-engineer` | — |

## Protocolo de handoff

Ver `handoffs.md`.
