# 04. AI Control and Governance

## 1. Purpose

The system should be AI-controlled in the sense that AI can interpret work, coordinate tools, and help run the business. It should **not** be uncontrolled. Governance is the difference between a valuable operational brain and a dangerous automation toy.

## 2. AI layers

### Layer 1 — Interaction intelligence
The conversational surface that understands:
- natural language
- task switching
- incomplete instructions
- role context
- device/channel context

### Layer 2 — Context compiler
Builds a structured packet from:
- tenant data
- current record(s)
- user role
- package/module availability
- site/job memory
- recent channel history
- device state

### Layer 3 — Policy and risk evaluator
Checks:
- permission
- tenant boundary
- financial sensitivity
- destructive action level
- communication impact
- external side effects
- confidence threshold

### Layer 4 — Tool executor
Calls module APIs/actions/services.

### Layer 5 — Audit and memory writer
Stores:
- intent summary
- context snapshot reference
- proposed actions
- approval status
- final tool results
- errors/refusals

## 3. Approval model

The safest scalable pattern is:
- AI may always **draft**, **analyze**, **summarize**, **recommend**, and **prepare**
- AI may **execute low-risk read actions** automatically
- AI may **execute low-risk write actions** when company policy allows it
- AI must **request approval** for higher-risk actions
- AI must **refuse** illegal or out-of-scope actions

## 4. Risk bands

### Band 0 — Read only
Examples:
- summarize jobs due today
- show unpaid invoices
- list customer communication issues

### Band 1 — Low-risk internal write
Examples:
- add note
- tag record
- draft checklist
- create internal reminder

### Band 2 — Operational write
Examples:
- reschedule visit
- assign worker
- create quote draft
- update checklist status

### Band 3 — External impact
Examples:
- send customer message
- publish campaign
- create invoice and deliver it
- trigger payment reminder

### Band 4 — Sensitive / irreversible / financial
Examples:
- approve expense
- mark payment settled manually
- delete records
- alter package/permissions
- issue refund
- cancel major jobs en masse

Each tenant should be able to configure which bands require approval.

## 5. Approval queue pattern

Every proposed action should become a structured item containing:
- summary
- target entities
- proposed tool call(s)
- risk band
- confidence
- reason and evidence
- tenant/user/device context
- expiry window

This lets the system function as a visible calibration layer while trust is being built.

## 6. AI execution contract

Every tool call should include:
- `company_id`
- `user_id` or acting principal
- origin channel/device
- idempotency key
- requested action
- approved flag / approval reference if required
- context snapshot reference

This makes replay, audit, and rollback possible.

## 7. Memory model

### Company memory
Policies, vocabulary, package state, default preferences.

### User memory
Personal preferences, workflow habits, assigned patterns.

### Site/job memory
Access instructions, recurring issues, proof rules, customer preferences.

### Channel memory
Recent customer conversation state and handoff continuity.

### Device memory
Offline queue, capability set, recent cache, local task state.

### AI memory
Prior reasoning artifacts and correction signals, but never as the only source of operational truth.

## 8. Tool design principles

Every AI tool should be:
- narrow in purpose
- idempotent where possible
- tenant-scoped
- auditable
- validated by form request/rules object
- driven by module services/actions rather than controller fragments

## 9. Refusal and fallback behavior

If AI cannot act safely, it should:
1. explain what is missing
2. offer safe next steps
3. create a draft/proposal instead of executing
4. request explicit approval or confirmation when policy allows

## 10. Explainability standard

After action, the system should be able to produce a plain-language summary:
- what you asked
- what context was used
- what was changed
- what remained unchanged
- what follow-up is recommended

## 11. Governance interfaces

The system needs dedicated governance surfaces:
- pending approvals queue
- AI run log
- policy settings by company/package
- confidence and refusal analytics
- tool registry visibility
- memory inspection/debug view

Filament is an excellent place for these.

## 12. AI should not bypass modules

Never let AI write directly into arbitrary tables without module-owned validation and service rules. AI should act through the same business contracts the rest of the system uses.

## 13. Evaluation loop

Track these metrics:
- approval acceptance rate
- override rate
- correction rate
- refusal rate
- execution success rate
- hallucination/invalid attempt rate
- time saved by AI-driven proposals

This turns AI quality into an operationally measurable system rather than a vibe.

## 14. Final governance sentence

The system should treat AI as a supervised operational orchestrator: powerful enough to run work, constrained enough to be trusted, and observable enough to improve continuously.
