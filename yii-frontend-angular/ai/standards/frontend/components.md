# Standard: Componentes e Padrões de UI

Padrões de UI reutilizáveis estabelecidos no projeto. Usar antes de inventar novo markup.

---

## Diretiva `bs-tooltip`

**Arquivo**: `app/directives/bsTooltip.js`

Inicializa Bootstrap Tooltip em elementos renderizados dinamicamente pelo AngularJS.
NÃO usar `DOMContentLoaded` global — não funciona com digest cycle do Angular.

**Uso**:
```html
<label bs-tooltip="Descrição do tooltip" tooltip-placement="top">Label visível</label>
```

**Atributos**:
- `bs-tooltip` — texto do tooltip (obrigatório)
- `tooltip-placement` — `top` (padrão), `bottom`, `left`, `right`

**Ciclo de vida**: a diretiva chama `tip.dispose()` no `$destroy` do scope — sem memory leak.

---

## Nav-tabs horizontais para nested states (UI-Router)

Usar quando uma página pai tem child states (≤4 tabs). Padrão para abas acima do conteúdo.

```html
<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2"
       ui-sref="estado.filho({id: $ctrl.id})"
       ui-sref-active="active">
      <i class="bi bi-icon"></i>
      Label
    </a>
  </li>
</ul>
<div ui-view></div>
```

**Regras**:
- `ui-sref-active="active"` — adiciona classe `active` via UI-Router (não usar `ng-class` manual)
- Badges e ícones de status ficam inline na tab (sem `ms-auto d-none d-md-inline`)
- NÃO usar `nav-pills flex-column` para nested states com ≤4 tabs

---

## Spinner no botão de submit

Padrão enquanto `$ctrl.saving === true`. Usar em TODOS os botões de submit de formulário.

```html
<button type="submit" class="btn btn-primary" ng-disabled="$ctrl.saving">
  <span ng-if="!$ctrl.saving"><i class="bi bi-save"></i> Salvar</span>
  <span ng-if="$ctrl.saving">
    <span class="spinner-border spinner-border-sm me-1" role="status"></span>Salvando...
  </span>
</button>
```

> Reuso atual: 2 (user-create, tab-info). Candidato a skill quando atingir 3.

---

## Breadcrumb de navegação

Usar no topo de páginas de criação e edição para dar contexto de onde o usuário está.

```html
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a ui-sref="estado-pai" class="text-decoration-none">Label pai</a>
    </li>
    <li class="breadcrumb-item active">Label atual</li>
  </ol>
</nav>
```

> Reuso atual: 2 (user-create, user-edit). Candidato a skill quando atingir 3.

---

## Loading spinner (lista/página)

Substitui `alert alert-info` para estados de carregamento. Usar quando há lista ou dados assíncronos.

```html
<div ng-if="$ctrl.loading" class="text-center py-5 text-muted">
  <div class="spinner-border text-primary mb-3" role="status">
    <span class="visually-hidden">Carregando...</span>
  </div>
  <p class="mb-0">Carregando...</p>
</div>
```

---

## Empty state (lista vazia)

Usar quando lista não tem resultados. Sempre incluir ícone + mensagem + CTA.

```html
<div class="text-center py-5 text-muted">
  <i class="bi bi-[icone-relevante] display-6 d-block mb-2 opacity-50"></i>
  Nenhum item encontrado.
  <div class="mt-2">
    <button class="btn btn-sm btn-outline-primary" ng-click="$ctrl.criar()">
      <i class="bi bi-plus-lg"></i> Criar o primeiro
    </button>
  </div>
</div>
```

---

## Card de auditoria (timestamps)

Usar para exibir `created_at` / `updated_at` fora do form de edição (não misturar com campos editáveis).

```html
<div class="card mt-3 border-0 bg-light">
  <div class="card-body py-2">
    <small class="text-muted d-flex gap-4 flex-wrap">
      <span><i class="bi bi-calendar-plus me-1"></i>Criado: {{ $ctrl.user.created_at | sqlDate }}</span>
      <span><i class="bi bi-calendar-check me-1"></i>Atualizado: {{ $ctrl.user.updated_at | sqlDate }}</span>
    </small>
  </div>
</div>
```

---

## Dropdown de ações em tabela

Substitui 3 botões inline por um único `bi-three-dots-vertical`. Usar em qualquer tabela com ≥2 ações por linha.

```html
<td class="text-end">
  <div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
      <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
      <li><button class="dropdown-item" ng-click="$ctrl.editar(item)">
        <i class="bi bi-pencil me-2 text-primary"></i>Editar
      </button></li>
      <li><hr class="dropdown-divider"></li>
      <li><button class="dropdown-item text-danger" ng-click="$ctrl.remover(item)">
        <i class="bi bi-trash me-2"></i>Excluir
      </button></li>
    </ul>
  </div>
</td>
```

**Requer**: Bootstrap JS bundle carregado (já presente em `index.html`).
