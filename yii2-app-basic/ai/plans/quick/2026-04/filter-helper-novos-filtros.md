# Plano: filter-helper-novos-filtros

**Data**: 2026-04-17
**Agente responsável**: backend-developer
**Status**: [x] Planning | [x] Execution | [ ] Verification | [ ] Done

---

## Objetivo

Adicionar 5 novos filtros ao `UserFilterHelper::handleArray()`, cada um demonstrando uma função de array PHP ainda não usada no helper.

## Impacto por camada

| Camada | O que muda |
|---|---|
| Service | Nenhuma mudança — `handleArray` já é chamado |
| Helper | `UserFilterHelper.php` — 5 novos blocos |
| Contrato de API | Nenhum — filtros são opcionais no `$filtros` recebido |

## Novos filtros

| Chave `$filtros` | Função PHP | O que faz |
|---|---|---|
| `busca` | `array_filter` + `stripos` | Busca case-insensitive em name ou email |
| `campos` | `array_intersect_key` + `array_fill_keys` | Projeção de campos (retorna só o solicitado) |
| `ordenar_por` | `usort` + spaceship `<=>` | Ordena por qualquer campo, com direção |
| `coluna` | `array_column` | Extrai uma coluna como array plano |
| `limite` | `array_splice` | Limita o total de resultados (in-place) |

## Critérios de aceite

- [ ] Cada filtro ativado apenas quando a chave estiver em `$filtros`
- [ ] Filtros existentes continuam funcionando sem alteração
- [ ] Comentários explicam o mecanismo interno da função PHP usada

## Handoff

**Arquivos modificados**: `services/UserFilterHelper.php`
**Decisões tomadas**: `coluna` e `limite` usam `return` antecipado / splice antes do return final — optei por manter a mesma posição lógica dos demais (no fluxo, antes do `return $users`).
