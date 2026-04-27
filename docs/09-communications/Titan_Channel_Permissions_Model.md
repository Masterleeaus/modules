# Titan Channel Permissions Model

## Purpose

The Titan Channel Permissions Model governs who can view, send, route, approve,
take over, configure, and automate communications across channels.

It covers:

- email
- SMS
- WhatsApp
- Telegram
- Messenger
- voice
- future channels

Permissions must be tenant-safe, role-aware, channel-specific, and compatible with
AEGIS approval rules.

---

## Core Principle

Communications permissions are not one generic boolean.

They are layered by:

- channel
- action
- audience
- scope
- approval level
- automation level

This prevents over-broad access.

---

## Runtime Location

Primary runtime:

`app/Platform/Communications/Permissions/`

Suggested structure:

- `Policies/`
- `Resolvers/`
- `Scopes/`
- `Approvals/`
- `ChannelRules/`
- `RoleMaps/`
- `Support/`

---

## Permission Categories

Minimum communication permission groups:

- view conversations
- send outbound messages
- reply to inbound messages
- manage templates
- manage automations
- take over conversations
- assign conversations
- access delivery logs
- configure channel credentials
- approve restricted sends

These should be defined per channel where needed.

---

## Example Permission Names

Examples:

- `comms.view`
- `comms.reply`
- `comms.assign`
- `comms.takeover`
- `comms.templates.manage`
- `comms.delivery.view`
- `comms.email.send`
- `comms.sms.send`
- `comms.whatsapp.send`
- `comms.voice.invoke`
- `comms.channel.configure`

Optional fine-grain channel namespaces are preferred.

---

## Scope Layers

Permissions should support scope such as:

- self
- team
- company
- assigned conversations
- specific channel
- specific queue
- specific campaign

This keeps access aligned to operations.

---

## Role Mapping

Typical role examples:

- operator
- dispatcher
- manager
- admin
- owner
- automation service account
- AI system role

Not every role should be able to configure channels or launch campaigns.

---

## Sensitive Action Classes

Higher-risk actions should require stricter checks:

- sending broadcast campaigns
- using premium SMS spend
- editing templates used in automations
- changing channel credentials
- overriding do-not-contact rules
- transferring ownership during active voice session
- sending finance-related communications

These often require approval, not direct permission alone.

---

## Approval-Aware Permissions

Some actions may be:

- allowed directly
- allowed only with approval
- fully denied

Examples:

- operator may draft WhatsApp finance reminder
- manager approval required before send
- owner may configure channel credential directly

This must integrate with AEGIS.

---

## AI and Automation Permissions

AI-executed channel actions must not bypass permissions.

Tool execution should validate:

- actor permission
- tenant ownership
- automation policy
- channel availability
- approval requirements

Automation service accounts should receive narrow permissions only.

---

## Conversation Ownership Checks

Permissions should interact with session ownership:

- who currently owns the thread
- whether takeover is allowed
- whether reply is restricted to assigned operator
- whether bot is allowed to continue

This prevents unauthorized interference in active conversations.

---

## Template Governance

Template operations need separate controls for:

- create
- edit
- publish
- retire
- bind to automation
- bind to campaigns
- bind to finance notices

Publishing should usually be more restricted than drafting.

---

## Credential and Channel Control

Credential access must be highly restricted.

Separate permissions for:

- view channel status
- rotate credentials
- reconnect channel
- change webhook targets
- disable channel
- switch routing priority

Credential visibility should be minimized.

---

## Queue and Assignment Permissions

For inbox operations, distinguish:

- view queue
- assign queue
- pull from queue
- reassign thread
- close thread
- reopen thread

This supports clean team operations.

---

## Delivery and Audit Visibility

Some roles may reply but not inspect full delivery diagnostics.

Separate permissions for:

- delivery event viewing
- webhook log viewing
- bounce/failure review
- compliance export
- audit export

This is especially important for managers and support engineers.

---

## Channel-Specific Constraints

Each channel may impose its own permission nuances.

Examples:

- SMS spend limits
- WhatsApp template approval usage
- Messenger policy windows
- Telegram bot scope restrictions
- voice escalation control

Channel rules should augment base permissions, not replace them.

---

## Tenant Boundary

All permission resolution must be company-scoped.

Tenant boundary rules apply to:

- conversations
- templates
- credentials
- campaigns
- audit records
- queues
- operators

No cross-tenant channel action is allowed.

---

## Recommended Storage Model

Use:

- permission seeders
- channel capability registry
- role-to-permission mapping
- optional policy manifest extensions

Module and platform services should resolve permissions consistently across:

- web panel
- API
- PWA
- AI tools
- jobs
- automation flows

---

## Integration Points

This model connects to:

- Unified Inbox
- Presence and Session Sync
- Omni Router
- AI Tool Registry
- AEGIS governance
- Workflow approvals
- Delivery tracking

It is a core runtime policy layer.

---

## Result

With a dedicated Channel Permissions Model, Titan can expose powerful omnichannel
communications safely, without collapsing sensitive channel control into broad admin access.
