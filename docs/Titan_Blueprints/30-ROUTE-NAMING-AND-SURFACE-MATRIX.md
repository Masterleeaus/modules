# Route Naming and Surface Matrix

## Purpose
Define one stable naming system across admin, user, API, panel, PWA, and Omni surfaces.

## Route families

### Web user routes
- `dashboard.user.<module>.index`
- `dashboard.user.<module>.show`
- `dashboard.user.<module>.create`
- `dashboard.user.<module>.edit`

### Super admin routes
- `superadmin.<module>.index`
- `superadmin.<module>.settings`

### API routes
- `api.v1.<module>.index`
- `api.v1.<module>.store`
- `api.v1.<module>.show`
- `api.v1.<module>.update`
- `api.v1.<module>.destroy`

### Filament resources
- resource pages should map to the same business nouns as routes
- avoid second naming system for the same record type

## Path guidance
- user web: `/account/<module>`
- super admin: `/super-admin/<module>`
- api: `/api/v1/<module>`
- pwa surface routes should map to task flows, not admin nouns

## Surface map
- web user = operator and business staff
- super admin = package/config/install/control
- Filament admin = dense operator/admin UX
- API = PWA/mobile/AI/tool access
- Omni = channel conversations and event ingress
