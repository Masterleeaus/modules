# Titan Zero Documentation

Layer: Interfaces + Surfaces
Scope: Voice interaction patterns, confirmation model, action grammar, and role-aware spoken control across Titan shells
Status: Draft v1
Depends On: AI, Communications, PWA + Nodes, Modules + Extensions, Workflows
Consumed By: Chat workspace, mobile PWAs, browser shells, future Titan Go and Command voice surfaces
Owner: Agent 08
Last Updated: 2026-04-15

---

## 1. Purpose

Define how voice should function as a first-class interface for Titan without creating a separate execution model from typed chat and structured UI actions.

## 2. Why it exists

Titan is intended to act like an operating system, especially on mobile and in the field. Voice reduces friction when a worker is moving, driving, gloved, or busy, and it accelerates quick approvals and lookup for owners and managers. A formal voice model prevents future implementations from becoming shallow dictation widgets or unsafe direct-execution shortcuts.

## 3. Core responsibilities

- capture and normalize voice input into Titan’s shared intent model
- preserve one action grammar across typed, clicked, and spoken interaction
- define confirmation rules for low-risk versus high-risk actions
- support role-specific voice experiences across command, worker, money, and portal shells
- return spoken and on-screen summaries that match system truth

## 4. Boundaries

### In scope

- speech capture patterns
- voice command parsing and confirmation UX
- spoken result summaries
- interruption and correction behavior
- role-aware voice shortcuts
- accessibility and hands-busy interaction patterns

### Out of scope

- telephony backend implementation
- model-provider routing internals
- audio codec and transport specifics
- domain workflow logic
- device-native hardware integrations beyond interface expectations

## 5. Architecture

## 5.1 One grammar rule

Voice must use the same underlying action model as typed chat and structured actions.

Example:

- typed: “reschedule Smith visit to Friday morning”
- spoken: same meaning
- button flow: same outcome

All three should resolve through the same validation, policy, workflow, and module action path.

## 5.2 Voice interaction loop

Recommended loop:

1. user speaks
2. transcript is captured
3. workspace context is attached
4. intent is classified
5. Titan generates a parsed action preview or direct answer
6. if risk is meaningful, user confirms or edits
7. action executes through shared services
8. result appears in thread and can be spoken back

## 5.3 Risk tiers

### Tier 1 — no-confirmation informational requests

Examples:

- “what jobs do I have today”
- “how many overdue invoices do we have”
- “show me the next site”

### Tier 2 — soft confirmation operational actions

Examples:

- “mark me on site”
- “message the customer I’m 10 minutes away”
- “open the invoice draft”

### Tier 3 — explicit confirmation required

Examples:

- create or cancel bookings
- send customer-facing financial messages
- approve payments or refunds
- finalize quotes/invoices
- complete workflow steps with compliance impact

### Tier 4 — step-up confirmation or blocked

Examples:

- delete records
- run cross-tenant or package-sensitive actions
- actions beyond current permission
- anything governed by AEGIS safe mode

## 5.4 Role-aware voice shells

### Worker voice shell

Priorities:

- status updates
- route/site lookup
- checklist progress
- issue capture
- customer arrival/completion messages
- proof-of-service prompts

Design rule:

keep utterances short and forgiving.

### Command voice shell

Priorities:

- quick approvals
- high-level summaries
- staff and job lookup
- exceptions and alerts
- direct launch to deeper surfaces

### Money voice shell

Priorities:

- invoice lookup
- overdue summaries
- collections prompts
- quote status questions
- confirmation before any money-changing action

### Portal voice shell

Priorities:

- guided help
- booking/status lookup
- invoice explanation
- simple approval or support requests

## 5.5 Display + speech dual mode

Voice should rarely be audio-only.

Preferred pattern:

- parse spoken input
- show concise visual summary
- optionally read summary aloud
- show action buttons for confirm, edit, cancel

This improves safety, accessibility, and trust.

## 5.6 Interrupt and correction behavior

Voice systems must accept correction naturally.

Examples:

- “no, next Friday”
- “cancel that”
- “I meant the Jones site”
- “don’t send, just draft it”

Corrections should modify the pending action object rather than restarting the whole flow.

## 6. Contracts

## 6.1 Input contract

Voice interface should provide:

- transcript text
- confidence or uncertainty hints if available
- current shell/panel/app mode
- entity context
- user identity and role
- tenant/package context
- device capability hints

## 6.2 Output contract

Voice-enabled responses should support:

- spoken summary text
- narrative message in thread
- parsed action preview
- confirmation requirement level
- optional structured cards or tables
- execution outcome summary

## 7. Runtime behavior

## 7.1 Confirmation model

Risk determines confirmation, not transport. A spoken command should never bypass normal workflow safeguards merely because it came through voice.

## 7.2 Noise and ambiguity handling

When confidence is low, Titan should:

- ask a narrow clarifying question
- present the top interpretation visually
- avoid acting until clarified if the action carries risk

## 7.3 Hands-busy mode

For field use, voice should support minimal-tap operation:

- read short summaries
- offer numbered choices
- allow simple confirmations like “yes”, “no”, “repeat”, “next”, “call customer”, “send ETA”

## 8. Failure modes

- **parallel grammar drift:** spoken actions behave differently than typed ones
- **unsafe execution:** voice bypasses approvals or policy checks
- **transcript ambiguity:** wrong customer/site/job is inferred
- **audio overload:** responses too long to be useful in real conditions
- **context mismatch:** command interpreted without current shell or entity context

## 9. Dependencies

Upstream:

- Titan Zero parsing and action proposal
- module actions and workflow guards
- communications adapters for spoken or follow-up actions
- PWA/device capability surfaces

Downstream:

- mobile shells
- command centre quick actions
- worker task flows
- customer portal guided voice interactions

## 10. Open questions

- which devices and browsers get first-wave voice support
- whether spoken playback is always enabled or role-configurable
- how far offline voice should go before local models are added
- whether voice transcripts should always be saved to thread history

## 11. Implementation notes

- start with browser/device-native speech capture where possible
- keep confirmation UI close to the chat thread and current record context
- use the same action and policy path as typed commands
- keep voice summaries short, especially for field and mobile command shells


## 11. Shared confirmation doctrine

Voice should never create a shadow execution path. High-risk commands must pass through the same approval and policy layers as typed actions. Confirmation can be:

- explicit verbal yes/no
- on-screen confirm button
- safe preview card in chat/PWA

### Typical high-risk actions

- send invoice or reminder
- change schedule affecting staff/customers
- approve payment or financial action
- publish customer-facing content
- trigger automation with external effects

## 12. Device and shell expectations

Because node and PWA docs treat browser/device shells as role-specific nodes, the voice layer should support different capability envelopes:

- browser PWA: mic capture, short spoken responses, interruption handling
- tablet/desktop: denser transcript + action preview
- field shell: large controls, offline transcript queue if possible, concise confirmations
- command shell: quick approvals, lookup, dispatch adjustments

## 13. Failure handling

Voice UX should degrade safely:

- if transcript confidence is low, show preview instead of executing
- if device is offline, queue only low-risk notes/tasks unless domain supports offline write
- if the action needs unavailable data, ask a narrowing question rather than guessing
- always expose the parsed intent visibly before significant execution


## 12. Voice as a shared intent path

Voice must not become a separate execution model. Spoken commands should resolve into the same validated intent and action contracts used by typed chat and clicked controls.

That means voice should be treated as:
- another command entry path
- another confirmation UX
- another summary output mode

It should not be treated as a bypass around services, policies, approvals, or workflows.

## 13. High-risk confirmation ladder

Recommended confirmation tiers:

### Low risk
Examples: lookup, summarize, open record, read next task.
Action may execute immediately with a short spoken and visual confirmation.

### Medium risk
Examples: mark arrived, send routine update, create draft.
Action should use concise confirm/undo patterns.

### High risk
Examples: cancel visit, approve payment, send money request, bulk reassign schedule.
Action must require explicit confirmation and visible review before execution.

## 14. Surface-specific voice modes

### Field Worker
Prioritize hands-busy actions, checklist progression, proof prompts, and site-memory lookup.

### Command
Prioritize quick approvals, alert triage, dispatch lookup, and exception summaries.

### Money
Prioritize balances, overdue lists, risk summaries, and draft-only financial actions unless elevated confirmation is provided.
