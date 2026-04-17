# Standard: Styling — Layout e Classes CSS

Padrões visuais definidos em `app/app.css`. Bootstrap 5.3.3 CDN-only.

## Layout sidebar (dashboard)

```
body
└── .app-mobile-header        # navbar só em mobile (display:none por padrão, flex em <768px)
└── div.d-flex
    ├── nav.app-sidebar        # sidebar fixa, 240px, bg-primary, sticky top:0
    │    └── d-none d-md-flex  # oculta em mobile
    ├── div.offcanvas          # id="sidebarMobile" — mesmo conteúdo, só mobile
    └── main.app-content       # flex:1, padding:2rem, background:#f7f8fa
```

**Regras:**
- `app-sidebar` usa `position: sticky; top: 0; height: 100vh` — não usar `fixed` (quebra o flex)
- Nav links da sidebar: `.nav-link` com `color: rgba(255,255,255,.7)` → ativo: `rgba(255,255,255,.2)`
- Mobile: sidebar some (`d-none d-md-flex`), offcanvas abre via `data-bs-toggle="offcanvas"`
- `app-content` em mobile: `padding: 1rem`

## CSS customizado

Arquivo: `app/app.css` — linkado no `<head>` após Bootstrap CSS, antes de qualquer outro `<link>`.

Seções:
- `.app-sidebar` / `.app-content` — layout principal
- `.app-mobile-header` — header mobile
- `.nav-tabs .nav-link` — cursor pointer
- `.nav-pills` — pills verticais para nested states (ver `components.md`)
