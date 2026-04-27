# API, Tooling, and External Surface Blueprint

## Principle
The same domain engine should be callable from multiple surfaces without rewriting core behavior.

## Supported surfaces
- web controllers
- Filament admin/user panels
- REST/JSON APIs
- PWA/mobile clients
- queued jobs
- console commands
- AI tool execution
- external integrations/webhooks

## API layer
Each module may expose `Routes/api.php` for domain endpoints.

API controllers should:
- validate input
- resolve tenant/auth context
- call domain actions/services
- return transformed resources

## Tool execution layer
AI or automation tools should not call raw model code directly.
They should call registered actions/services through a tool registry or adapter layer.

## External integration layer
Recommended capabilities:
- inbound webhooks
- outbound webhooks
- retry policies
- signature verification
- event normalization
- idempotent handling

## Resource transformation
Use API resources/transformers so:
- web shape can differ from API shape
- mobile payloads stay compact
- AI/tool payloads can be normalized

## Stability rule
Public route names and public API contracts should be versioned or evolved deliberately.
Avoid casual breaking changes once surfaces are consumed externally.
