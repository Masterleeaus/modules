# Route Naming and Surface Matrix

## Purpose
Define one stable naming system across admin, user, API, panel, PWA, and Omni surfaces.

## Convention

Route names follow the pattern: `{surface}.{resource}.{action}`

- `{surface}` — lowercase surface identifier (see surface map below)
- `{resource}` — plural noun in snake_case (e.g. `jobs`, `job_types`)
- `{action}` — snake_case verb or REST action (e.g. `index`, `show`, `update_status`)

**Rules:**
- Always use snake_case (never kebab-case) in route name segments
- Resource names are always plural
- Non-CRUD actions are prefixed with a verb (e.g. `update_status`, `extend_trial`)
- Route names must always be present — no anonymous routes

## Surface map

| Surface        | Prefix       | URL prefix    |
|----------------|--------------|---------------|
| Platform/Super | `platform.`  | `/platform`   |
| Owner/Admin    | `owner.`     | `/owner`      |
| Technician     | `technician.`| `/technician` |
| Client Portal  | `portal.`    | `/client`     |
| Public         | `public.`    | (root)        |
| API            | `technician.`| `/api/technician` |

## Route families

### Platform routes
- `platform.dashboard`
- `platform.organizations.show`
- `platform.organizations.update`
- `platform.organizations.subscription.update`
- `platform.organizations.extend_trial`
- `platform.organizations.activate`

### Owner/Admin routes
- `owner.dashboard`
- `owner.jobs.index`, `owner.jobs.show`, `owner.jobs.create`, `owner.jobs.store`
- `owner.jobs.edit`, `owner.jobs.update`, `owner.jobs.destroy`
- `owner.jobs.update_status`, `owner.jobs.reschedule`, `owner.jobs.reassign`
- `owner.reports.jobs_by_type`, `owner.reports.job_profitability`
- `owner.reports.technician_performance`
- `owner.setup.job_types.store`, `owner.setup.job_types.destroy`

### Technician routes
- `technician.dashboard`
- `technician.jobs.today`, `technician.jobs.index`, `technician.jobs.show`
- `technician.jobs.update_status`, `technician.jobs.update_notes`
- `technician.jobs.update_customer_notes`
- `technician.jobs.checklist_item.toggle`
- `technician.jobs.photos.store`, `technician.jobs.photos.destroy`
- `technician.jobs.line_items.store`, `technician.jobs.line_items.update`
- `technician.jobs.line_items.destroy`

### Client Portal routes
- `portal.login`, `portal.login.send`, `portal.auth`
- `portal.dashboard`, `portal.logout`

### Public routes
- `public.estimates.show`, `public.estimates.accept`, `public.estimates.decline`
- `public.reviews.show`, `public.reviews.store`

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
- owner web: `/owner/<resource>`
- technician web: `/technician/<resource>`
- api: `/api/technician/<resource>`
- client portal: `/client/<page>`
- public: `/<resource>/{token}`
- pwa surface routes should map to task flows, not admin nouns
