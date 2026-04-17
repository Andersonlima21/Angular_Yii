# Plano: visual-refactor-user-friendly

**Data**: 2026-04-17
**Agentes**: `ui-ux-assistant` (define) + `frontend-developer` (implementa) — coordenado por `tech-lead`
**Status**: [x] Planning | [x] Execution | [x] Verification | [x] Done

---

## Objetivo

Refatorar a camada visual do frontend mantendo Bootstrap 5.3.3 CDN-only e AngularJS 1.x, transformando a interface de "protótipo funcional" em uma UI com hierarquia clara, feedback adequado ao usuário final e identidade visual consistente.

**Fora de escopo**: lógica de negócio, services, controllers, contrato de API.

---

## Decisões de design tomadas

| # | Decisão | Escolha |
|---|---|---|
| 1 | Layout geral | **Sidebar fixa** (dashboard) — `bg-primary`, 240px, conteúdo à direita |
| 2 | Badges PHP no filtro | **Tooltip** via diretiva `bs-tooltip` — label limpa, badge como `title` |
| 3 | Ações na tabela | **Dropdown** com `bi-three-dots-vertical` por linha |
| 4 | Tabs de edição | **`nav-pills` verticais** na coluna lateral esquerda |

---

## Critérios de aceite

- [x] Sidebar no HTML — `grep "app-sidebar" index.html` → confirmado (T1)
- [x] Sidebar visível em desktop (≥ md); colapsável em mobile via offcanvas — testado pelo usuário
- [x] Nenhuma rota quebrada — todos os `ui-sref` e `ui-sref-active` funcionando — testado pelo usuário
- [x] Dropdown de ações cobre: Editar, Ativar/Inativar, Excluir — testado pelo usuário
- [x] Badges PHP removidos — `grep` retornou 0 matches (T2)
- [x] Pills verticais refletem o estado ativo via `ui-sref-active` — testado pelo usuário
- [x] Loading state — `grep "spinner-border"` confirmado (T4)
- [x] Empty state com ícone e call-to-action — implementado em `user-list.html` (T4)
- [x] `node --check app/**/*.js` sem erros — confirmado (T7)
- [x] Testado no browser (`npx http-server -p 5500`) — aprovado pelo usuário

---

## Riscos e rollback

| Risco | Mitigação |
|---|---|
| Sidebar quebra mobile | Usar `d-none d-md-flex` no sidebar; adicionar botão offcanvas para mobile |
| Pills verticais dentro de `user-edit` criam colunas aninhadas profundas | Testar responsividade em cada breakpoint; usar `col-md-2 / col-md-10` |
| Tooltips não disparam em conteúdo dinâmico AngularJS | Usar diretiva `bs-tooltip` que inicializa no `link` do DOM — NÃO usar `DOMContentLoaded` global |
| `ui-sref-active` para de funcionar com nova estrutura de pills | Manter exatamente os mesmos nomes de state — só muda a classe da tag (`nav-link`) |

**Rollback**: toda mudança é HTML/CSS puro. Git revert em qualquer tarefa é instantâneo e sem impacto em JS/services.

---

## Tarefas em ordem

### TAREFA 1 — Criar `app/app.css` + refatorar `index.html` para sidebar

**Contexto budget**: `context-pack.md` (ordem de scripts)

**Arquivos**:
- `index.html` ← refatorar estrutura body
- `app/app.css` ← CRIAR (novo arquivo de estilos customizados)

**O que fazer**:

1. Criar `app/app.css` com variáveis CSS e estilos da sidebar:

```css
/* Sidebar */
.app-sidebar {
  width: 240px;
  min-height: 100vh;
  flex-shrink: 0;
}
.app-sidebar .nav-link {
  color: rgba(255,255,255,.75);
  border-radius: 6px;
  padding: .5rem 1rem;
  transition: background .15s;
}
.app-sidebar .nav-link:hover,
.app-sidebar .nav-link.active {
  color: #fff;
  background: rgba(255,255,255,.15);
}
.app-sidebar .nav-link .bi {
  width: 20px;
}
.app-content {
  flex: 1 1 0;
  min-width: 0;
  padding: 2rem;
  background: #f7f8fa;
  min-height: 100vh;
}
/* Mobile: ocultar sidebar e mostrar toggler */
@media (max-width: 767.98px) {
  .app-sidebar { display: none !important; }
  .app-mobile-header { display: flex !important; }
}
.app-mobile-header { display: none; }
```

2. Refatorar `index.html`:
   - Remover `<nav class="navbar...fixed-top">` e `body { padding-top: 70px }`
   - Estrutura nova:

```html
<body>
<!-- Mobile header (só mobile) -->
<header class="app-mobile-header navbar navbar-dark bg-primary px-3">
  <span class="navbar-brand"><i class="bi bi-people-fill"></i> YiiApp</span>
  <button class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
    <span class="navbar-toggler-icon"></span>
  </button>
</header>

<!-- Offcanvas (mobile) + Sidebar (desktop) -->
<div class="d-flex">
  <nav class="app-sidebar bg-primary d-none d-md-flex flex-column p-3 gap-1" id="sidebarDesktop">
    <!-- brand + nav links -->
  </nav>

  <!-- Offcanvas para mobile -->
  <div class="offcanvas offcanvas-start bg-primary text-white" id="sidebar" style="width:240px">
    <!-- mesmo conteúdo do sidebar desktop -->
  </div>

  <main class="app-content">
    <div ui-view></div>
  </main>
</div>

<!-- Bootstrap JS (para Offcanvas + Dropdown + Tooltip) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- app scripts na ordem padrão -->
</body>
```

3. Adicionar link para `app/app.css` no `<head>` (após Bootstrap CSS)

> ⚠️ **Atenção**: Bootstrap JS bundle ainda não estava no `index.html` — precisa ser adicionado para Dropdown e Offcanvas funcionarem. É CDN Bootstrap oficial, sem nova dependência externa.

---

### TAREFA 2 — Diretiva `bs-tooltip` para substituir badges PHP

**Contexto budget**: `context-pack.md` (ordem de scripts, padrão de diretivas)

**Arquivos**:
- `app/directives/bsTooltip.js` ← CRIAR
- `index.html` ← adicionar script
- `app/components/user-list/user-list.html` ← aplicar diretiva + converter badges

**O que fazer**:

1. Criar diretiva (limpa, sem dependência):

```js
angular.module('yiiApp').directive('bsTooltip', function() {
  return {
    restrict: 'A',
    link: function(scope, element, attrs) {
      var tip = new bootstrap.Tooltip(element[0], {
        title: attrs.bsTooltip,
        placement: attrs.tooltipPlacement || 'top',
        trigger: 'hover'
      });
      scope.$on('$destroy', function() {
        tip.dispose();
      });
    }
  };
});
```

2. No `user-list.html`, converter cada badge:
   - Antes: `<span class="badge bg-primary me-1">array_filter</span>Status`
   - Depois: `<label class="form-label small" bs-tooltip="array_filter: filtra registros no backend">Status</label>`

3. Adicionar `app/directives/bsTooltip.js` no `index.html` (após `phoneMask.js`)

> ⚠️ **Ordem obrigatória**: Bootstrap JS bundle (adicionado na T1) DEVE aparecer antes de `app.js` no `index.html`. A diretiva `bsTooltip` usa `new bootstrap.Tooltip()` em runtime — se o bundle não estiver carregado antes do primeiro digest, vai lançar `ReferenceError: bootstrap is not defined`.

---

### TAREFA 3 — Dropdown "Ações" na tabela de usuários

**Contexto budget**: `context-pack.md` (sem mudança em controller)

**Arquivos**:
- `app/components/user-list/user-list.html`

**O que fazer** (só no bloco `MODO NORMAL`):

Substituir o bloco de botões inline:
```html
<!-- ANTES -->
<td class="text-end">
  <button ... ng-click="$ctrl.edit(u.id)">Editar</button>
  <button ... ng-click="$ctrl.toggleActive(u)">...</button>
  <button ... ng-click="$ctrl.remove(u)">...</button>
</td>
```

Por dropdown:
```html
<!-- DEPOIS -->
<td class="text-end">
  <div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary"
            data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
      <li>
        <button class="dropdown-item" ng-click="$ctrl.edit(u.id)">
          <i class="bi bi-pencil me-2 text-primary"></i>Editar
        </button>
      </li>
      <li>
        <button class="dropdown-item" ng-click="$ctrl.toggleActive(u)">
          <i class="bi me-2" ng-class="u.is_active ? 'bi-slash-circle text-warning' : 'bi-check-circle text-success'"></i>
          {{ u.is_active ? 'Inativar' : 'Ativar' }}
        </button>
      </li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <button class="dropdown-item text-danger" ng-click="$ctrl.remove(u)">
          <i class="bi bi-trash me-2"></i>Excluir
        </button>
      </li>
    </ul>
  </div>
</td>
```

---

### TAREFA 4 — Loading state e empty state na listagem

**Contexto budget**: `context-pack.md`

**Arquivos**:
- `app/components/user-list/user-list.html`

**O que fazer**:

Loading (substituir `<div ng-if="$ctrl.loading" class="alert alert-info">`):
```html
<div ng-if="$ctrl.loading" class="text-center py-5 text-muted">
  <div class="spinner-border text-primary mb-3" role="status"></div>
  <p class="mb-0">Carregando usuários...</p>
</div>
```

Empty state (dentro da `<tbody>`, `ng-if` existente):
```html
<tr ng-if="($ctrl.users | filter:$ctrl.matchFilter).length === 0">
  <td colspan="5" class="text-center py-5 text-muted">
    <i class="bi bi-people display-6 d-block mb-2 opacity-50"></i>
    Nenhum usuário encontrado.
    <div class="mt-2">
      <button class="btn btn-sm btn-outline-primary" ng-click="$ctrl.create()">
        <i class="bi bi-plus-lg"></i> Criar o primeiro usuário
      </button>
    </div>
  </td>
</tr>
```

---

### TAREFA 5 — Pills verticais no `user-edit`

**Contexto budget**: `context-pack.md` (estados UI-Router, `ui-sref-active`)

**Arquivos**:
- `app/components/user-edit/user-edit.html`
- `app/app.css` ← adicionar estilos das pills de edição

**O que fazer**:

Substituir `<ul class="nav nav-tabs mb-3">` por layout com coluna lateral:

```html
<!-- Header do usuário (preservar) -->
<div class="d-flex justify-content-between align-items-center mb-4">...</div>

<!-- Layout pills + conteúdo -->
<div class="row g-0">
  <!-- Pills verticais -->
  <div class="col-md-2">
    <div class="nav flex-column nav-pills gap-1 pe-3">
      <a class="nav-link d-flex align-items-center gap-2"
         ui-sref="editUser.info({id: $ctrl.userId})"
         ui-sref-active="active">
        <i class="bi bi-person"></i> <span>Info</span>
      </a>
      <a class="nav-link d-flex align-items-center gap-2"
         ui-sref="editUser.configs({id: $ctrl.userId})"
         ui-sref-active="active">
        <i class="bi bi-sliders"></i>
        <span>Configs</span>
        <span class="badge bg-secondary ms-auto">{{ $ctrl.user.configs.length || 0 }}</span>
      </a>
      <a class="nav-link d-flex align-items-center gap-2"
         ui-sref="editUser.profiles({id: $ctrl.userId})"
         ui-sref-active="active">
        <i class="bi bi-person-vcard"></i>
        <span>Profile</span>
        <i class="bi ms-auto"
           ng-class="$ctrl.user.profiles.length ? 'bi-check-circle-fill text-success' : 'bi-dash-circle text-muted'"></i>
      </a>
    </div>
  </div>

  <!-- Conteúdo da tab -->
  <div class="col-md-10">
    <div ui-view></div>
  </div>
</div>
```

Adicionar em `app.css`:
```css
/* Pills de edição de usuário */
.nav-pills .nav-link {
  color: #495057;
  font-size: .9rem;
}
.nav-pills .nav-link.active {
  background-color: var(--bs-primary);
  color: #fff;
}
```

---

### TAREFA 6 — Melhorias em `user-create` e `tab-info`

**Contexto budget**: `context-pack.md`

**Arquivos**:
- `app/components/user-create/user-create.html`
- `app/components/tab-info/tab-info.html`

**user-create**: Adicionar page header com breadcrumb + floating labels Bootstrap 5:
```html
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a ui-sref="users" class="text-decoration-none">Usuários</a>
    </li>
    <li class="breadcrumb-item active">Novo usuário</li>
  </ol>
</nav>
<h4 class="mb-4"><i class="bi bi-person-plus-fill"></i> Novo usuário</h4>

<!-- Floating labels no form -->
<div class="form-floating mb-3">
  <input type="text" class="form-control" id="name" placeholder="Nome"
         ng-model="$ctrl.form.name" required maxlength="255">
  <label for="name">Nome completo</label>
</div>
<div class="form-floating mb-3">
  <input type="email" class="form-control" id="email" placeholder="Email"
         ng-model="$ctrl.form.email" required maxlength="255">
  <label for="email">E-mail</label>
</div>
```

**tab-info**: Mover timestamps para fora do form (card separado de auditoria):
```html
<!-- Após o form de edição -->
<div class="card mt-3 border-0 bg-light">
  <div class="card-body py-2">
    <small class="text-muted d-flex gap-4">
      <span><i class="bi bi-calendar-plus me-1"></i>Criado: {{ $ctrl.user.created_at | sqlDate }}</span>
      <span><i class="bi bi-calendar-check me-1"></i>Atualizado: {{ $ctrl.user.updated_at | sqlDate }}</span>
    </small>
  </div>
</div>
```

---

### TAREFA 7 — Verificação e testes

**Contexto budget**: `context-pack.md` + `workflows/testing.md`

**Verificações por comando (rodar antes de abrir o browser):**
```bash
node --check app/**/*.js
grep "app-sidebar" index.html
grep "badge bg-primary" app/components/user-list/user-list.html   # deve retornar vazio
grep "spinner-border" app/components/user-list/user-list.html
grep "bootstrap.bundle" index.html
grep "bsTooltip" index.html
```

**Verificações manuais no browser:** ✅ aprovadas pelo usuário em 2026-04-17
- [x] Abrir `http://localhost:5500` (`npx http-server -p 5500 -c-1`) com backend em `:8080`
- [x] Navegar: lista → novo usuário → criar → editar → todas as tabs
- [x] Testar dropdown: editar, ativar/inativar, excluir
- [x] Testar sidebar mobile (viewport < 768px) — offcanvas abre/fecha
- [x] Tooltips aparecem ao hover nos labels do filtro
- [x] Pills verticais: ativo reflete tab correta
- [x] Verificar que `alert-msg` funciona em tab-info e tab-configs

---

## Arquivos afetados (resumo)

| Arquivo | Operação |
|---|---|
| `index.html` | Refatorar (sidebar, bootstrap.bundle.js, app.css) |
| `app/app.css` | CRIAR |
| `app/directives/bsTooltip.js` | CRIAR |
| `app/components/user-list/user-list.html` | Refatorar (dropdown, loading, empty state, tooltips) |
| `app/components/user-edit/user-edit.html` | Refatorar (pills verticais) |
| `app/components/user-create/user-create.html` | Melhorar (breadcrumb, floating labels) |
| `app/components/tab-info/tab-info.html` | Ajustar (timestamps para card separado) |

**Não mudam**: todos os `.component.js`, services, `app.js`, rotas, lógica de negócio.

---

## Handoff

**Parou em**: ✅ DONE — todas as verificações concluídas em 2026-04-17.
**Próxima ação**: Subir servidores e testar os 7 itens manuais da T7.
**Arquivos modificados**:
- `index.html` — sidebar, Bootstrap JS bundle, app.css, bsTooltip.js
- `app/app.css` — CRIADO (sidebar, mobile header, nav-pills)
- `app/directives/bsTooltip.js` — CRIADO
- `app/components/user-list/user-list.html` — tooltips, dropdown, spinner, empty state
- `app/components/user-edit/user-edit.html` — pills verticais + breadcrumb
- `app/components/user-create/user-create.html` — breadcrumb + floating labels + spinner no botão
- `app/components/tab-info/tab-info.html` — card de auditoria separado do form
**Não modificados**: todos os `.component.js`, services, `app.js`, rotas
