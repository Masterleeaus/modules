# Titan Zero Documentation

Layer: Communications + Channels
Scope: Shared message templates, render contracts, personalization, channel adaptation
Status: Draft v1
Depends On: Titan Channel Architecture, Titan Routing and Failover, Titan Manifest System
Consumed By: Email engine, SMS engine, WhatsApp engine, Telegram engine, Messenger engine, Push engine, Voice engine, Omni router
Owner: Agent 09
Last Updated: 2026-04-15

---

## 1. Purpose

Define the shared template system Titan uses to render outbound and operator-authored messages consistently across channels.

## 2. Why it exists

Titan cannot afford a separate message-formatting system for every channel. Templates must be reusable, governable, tenant-safe, and adaptable so one approved communication intent can be rendered appropriately for email, SMS, WhatsApp, push, and voice.

A shared template layer gives Titan:

- one source of message structure
- one personalization model
- one approval-friendly content contract
- one downgrade path when channels support less formatting
- one audit trail from intent to rendered output

## 3. Core responsibilities

- store canonical message templates independent of transport
- resolve placeholders from approved data only
- support channel-specific render variants without duplicating business intent
- separate message content from routing and delivery behavior
- support localization and tenant branding
- preserve render logs for audit and replay

## 4. Boundaries

### In scope

- template entities and storage
- variables and placeholder contracts
- layout inheritance
- channel-specific render adapters
- localization and branding slots
- preview and render logging
- downgrade rules for constrained channels

### Out of scope

- channel provider SDK behavior
- retry logic
- routing preferences
- campaign segmentation logic
- freeform operator UI editors

## 5. Architecture

Templates should live under a dedicated shared layer:

```text
app/Platform/Communications/Templates/
├─ Entities/
├─ Layouts/
├─ Variants/
├─ Renderers/
├─ PlaceholderResolvers/
├─ Localization/
├─ Branding/
├─ Validators/
├─ Previews/
└─ Support/
```

The template system has four levels:

### Template intent

The message purpose, such as:

- booking confirmation
- job reminder
- overdue invoice notice
- worker late alert
- quote follow-up
- password reset

### Template structure

Defines the sections available to render:

- subject
- headline
- body
- summary
- call to action
- footer
- attachments or media references
- voice summary text

### Variant layer

Allows channel-appropriate outputs from one canonical template:

- rich email HTML
- plain email text
- SMS compact text
- WhatsApp rich text and action payload
- push notification title and body
- voice script summary

### Render context

Supplies approved data only, such as:

- company name
- contact name
- job reference
- booking date and time
- invoice amount
- secure action links
- site or worker labels

## 6. Contracts

### Placeholder contract

Templates may only use declared placeholders.

Example categories:

- tenant.*
- contact.*
- worker.*
- job.*
- invoice.*
- schedule.*
- links.*

No template should query the database at render time.

### Variant contract

Each template can expose one or more variants:

- default
- email_html
- email_text
- sms
- whatsapp
- push
- voice

### Branding contract

Branding should be applied as tokens rather than hardcoded HTML.

Examples:

- logo_url
- primary_color
- footer_signature
- support_phone

## 7. Runtime behavior

A normal render path should be:

1. approved message intent created
2. routing determines candidate channels
3. template chosen by intent type
4. placeholder resolver hydrates render context
5. channel renderer produces final payload
6. validation checks size and channel constraints
7. rendered payload stored for audit and dispatch

If a channel cannot support the full structure, Titan should downgrade rather than fail silently.

Examples:

- convert long email body into short SMS summary plus link
- strip unsupported buttons from basic WhatsApp tier
- reduce rich content to plain text for fallback channels

## 8. Failure modes

### Missing placeholders

If required variables are missing, render must fail before dispatch and create a visible operator or automation error.

### Oversized payloads

If channel output exceeds limits, Titan should either truncate according to policy, split according to channel rules, or reroute to a more suitable channel.

### Unsafe variables

Resolvers must refuse undeclared or cross-tenant variables.

### Broken localization

If localized text is missing, Titan should fall back to tenant default language and log the substitution.

## 9. Dependencies

Upstream:

- workflow engine
- automation engine
- signal engine
- AI tool actions
- tenant branding settings

Downstream:

- email engine
- SMS engine
- WhatsApp engine
- push engine
- voice engine
- routing engine
- delivery logs

## 10. Open questions

- Should template approval be optional or mandatory for high-risk message classes?
- Should operators be allowed tenant-level custom variants per channel?
- How should multi-brand tenants inherit shared layouts?

## 11. Implementation notes

Keep templates transport-agnostic at the core. Channel adapters should render from shared content contracts instead of storing separate business logic per channel. Render logs should preserve both selected template id and final rendered payload hash for replay and audit.
