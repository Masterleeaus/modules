# Titan Zero Documentation

Layer: Interfaces + Surfaces
Scope: Conversational operating system, chat-first workspace, structured handoff, and voice-facing interaction patterns
Status: Draft v1
Depends On: AI, Modules + Extensions, PWA + Nodes, Communications + Channels, Workflows
Consumed By: Titan chat workspace, Omni surfaces, future voice UI, admin and mobile shells
Owner: Agent 08
Last Updated: 2026-04-15

---

## 1. Purpose

Define the chat-first interaction model for Titan so that conversational flow, structured actions, dashboards, and voice controls work as one system instead of separate UI metaphors.

## 2. Why it exists

Titan is intended to feel like an operating system, not just a dashboard. That means users should be able to start from conversation, receive structured business results, hand off into forms or workflows when needed, and return to conversation without losing context. This document defines that interface grammar.

## 3. Core responsibilities

- provide the conversational workspace model for web and PWA shells
- define how chat hands off into structured actions, forms, tables, and approvals
- distinguish dashboard use from conversational use without fragmenting the product
- define voice-friendly interaction patterns over the same intent and action model
- keep interface semantics aligned with Titan Zero outputs and module actions

## 4. Boundaries

### In scope

- chat workspace layout
- message rendering patterns
- analytics-in-chat blocks
- structured handoff rules
- side panels and context panes
- voice-to-action interaction model
- relationship between command centre and chat workspace

### Out of scope

- model routing logic
- AI memory internals
- module contract ownership
- offline engine internals
- telephony transport implementation

## 5. Architecture

## 5.1 The conversational OS model

The preferred interface hierarchy is:

1. conversation establishes intent
2. Titan Zero returns narrative + structured results
3. user accepts, edits, or expands into a structured surface
4. underlying module actions execute through stable services
5. result is summarized back into the conversation thread

This makes conversation the front door, but not the only UI element.

## 5.2 Workspace anatomy

A full chat-first Titan workspace should have four visual zones:

### A. Thread zone

The persistent conversation, including:

- user instructions
- assistant narrative responses
- status updates
- approvals and denials
- short summaries of completed actions

### B. Structured result zone

Inline blocks inserted into the thread, such as:

- tables
- cards
- chart summaries
- checklists
- exception reports
- draft previews
- workflow state snapshots

### C. Context side pane

Entity-aware context, such as:

- current customer/site/job
- package or permission constraints
- recent activity
- pending approvals
- linked documents or attachments

### D. Action rail or canvas

Temporary structured surfaces for:

- editing a quote, booking, or invoice
- approving recommendations
- filling a form
- resolving conflicts
- reviewing exceptions

## 5.3 Canvas handoff rule

Conversation should hand off to a structured canvas when any of the following become true:

- more than a few fields require precise editing
- a user must review a table or checklist before continuing
- a workflow state change requires explicit confirmation
- approvals, signatures, or compliance artifacts are needed
- the result must remain editable outside the thread

The handoff should not feel like opening a different product. The canvas must inherit the same entity context, language, and AI support.

## 5.4 What belongs to chat versus dashboards

### Chat is best for

- intent capture
- summarization
- recommendations
- quick actions
- exceptions and follow-up
- approvals with context
- ad hoc analytical questions

### Dashboards are best for

- persistent monitoring
- queue and KPI visibility
- scan-based supervision
- comparison across many records
- rapid navigation to dense work areas

The product should allow movement between them in one click, with shared context.

## 5.5 Voice interface model

Voice should sit on top of the same action grammar as typed chat.

Recommended voice flow:

- capture speech
- transcribe
- classify intent with current workspace context
- show parsed action back to user when risk is non-trivial
- confirm where needed
- execute through the same action/service path
- summarize result in thread and optional spoken playback

Voice should never invent a second business grammar separate from typed interaction.

## 5.6 Role-aware conversational shells

### Command shell

For owners/managers:

- command summaries
- approvals
- cross-domain insights
- live risk and recommendation feed

### Worker shell

For field users:

- short commands
- checklist progress
- route and site context
- proof-of-service capture
- issue reporting

### Money shell

For finance users:

- overdue and payment actions
- quote/invoice lookup
- collections recommendations
- approval of sensitive money actions

### Portal shell

For customers:

- booking/help/status requests
- document access
- invoice and approval interactions
- guided flows with narrow action scope

## 6. Contracts

## 6.1 Input contract to Titan Zero from the interface

The workspace should pass:

- user message or voice transcript
- current shell/panel/app mode
- current entity context
- user and tenant identity
- active package/capabilities
- recent thread context
- device or node capability hints

## 6.2 Output contract from Titan Zero to the interface

Titan responses should be renderable as:

- narrative message
- cards
- tables
- charts
- workflow summaries
- approval requests
- suggested next actions
- confidence or risk indicators
- structured payloads for canvas handoff

## 6.3 Interface rendering contract

The UI layer should support rendering of:

- plain assistant text
- KPI cards
- tabular blocks
- Mermaid or workflow diagrams when useful
- code/config blocks where admin users need them
- editable drafts and approval cards

This is where Agenytics-style UI patterns are useful as reference, but the runtime contract remains internal to Titan.

## 7. Runtime behavior

## 7.1 Thread continuity

Every structured action should flow back into the thread as a concise event:

- what was proposed
- what changed
- whether it succeeded, failed, or needs review
- what the next best action is

## 7.2 Context preservation

If a user moves from desktop dashboard to mobile shell, the system should preserve:

- current entity scope
- pending action state
- conversational context where safe
- approval and draft status

## 7.3 Low-risk versus high-risk behavior

### Low-risk actions

May execute directly with short feedback.

### Medium-risk actions

Should display a parsed action card first.

### High-risk actions

Must require explicit confirmation and route through approval or governance surfaces.

## 8. Failure modes

- **chat-only trap:** forcing every task through prose even when structured editing is needed
- **canvas breakage:** handoff opens a disconnected form with no conversation context
- **voice ambiguity:** spoken command is interpreted without enough confirmation
- **surface drift:** dashboard, chat, and PWA surfaces use different status names or actions
- **overloaded thread:** too many large tables or forms dumped directly into the chat feed

## 9. Dependencies

Upstream:

- Titan Zero output envelopes
- module actions and workflow rules
- PWA/node context and device capabilities
- communications layer for cross-channel surfaces

Downstream:

- chat-first web shell
- mobile or PWA role-specific shells
- voice interface
- approval and exception handling surfaces

## 10. Open questions

- Should the first MVP implement one shared chat workspace plus role filters, or distinct chat shells per mode?
- How much of command-centre monitoring should also appear as conversational summaries by default?
- Which structured blocks are mandatory for v1: tables, cards, approvals, charts, and drafts?
- What is the minimum confirmation pattern for voice on mobile field apps?

## 11. Implementation notes

- Keep the chat-first shell separate from Titan Zero orchestration code.
- Let Filament host operator surfaces, but avoid forcing the entire conversational OS into Filament if a dedicated shell is cleaner.
- Reuse the same module actions and workflow transitions across chat, dashboards, PWAs, and voice.
- Use one shared vocabulary for entities, statuses, approvals, and next-step suggestions.


## 11. Output contract expectations

The conversational OS should assume Titan Zero returns more than prose. A usable response contract should support:

- narrative summary
- structured cards
- table payloads
- chart payloads
- workflow/approval payloads
- action suggestions with risk level
- entity references for side panels

This keeps the thread expressive while still enabling precise handoff into structured UI.

## 12. Canvas handoff rules

A canvas handoff should occur when the user needs precision, comparison, or bulk review.

### Trigger conditions

- more than one record must be compared
- approval/denial changes system state
- form entry has required validation
- map/timeline/board layout communicates better than prose
- voice or chat needs a review step before execution

### Return path rule

Every canvas must be able to summarize its result back into the thread so conversation remains the durable narrative surface.

## 13. Rich rendering guidance

Agenytics-style presentation patterns are useful here as reference only:

- charts inside thread blocks
- tables for operational comparisons
- Mermaid-like diagrams for workflow/state explanation
- code/config blocks for technical admin work

The rendering layer may borrow those patterns, but orchestration and execution remain inside Titan Zero + module actions.


## 13. Three-region screen doctrine

Advanced Titan screens should be understandable as three cooperating regions:

- **State region** — canonical records, metrics, queues, and entity status
- **Action region** — forms, approvals, transitions, and workflow operations
- **Intelligence region** — AI summary, risks, next-best actions, and command bar

This same pattern should apply in Filament panels, Blade views, and PWA shells so users do not need to relearn the product every time they switch surfaces.

## 14. Chat is not the only UI

The conversational OS is primary, but not exclusive. Titan must support scan-based work, dense dashboards, approvals, and role-shaped apps. Conversation should therefore hand off cleanly into:
- dashboard cards
- structured forms
- operator tables
- approval drawers
- entity side panels
- voice confirmations

The design goal is not to force every task into a message thread. The goal is to make conversation the natural entry point and coordination layer around structured work.

## 15. Inward integration contract

The chat workspace must talk inward to Titan Zero and module services, not sideways into duplicate mini-app logic. A good handoff contract includes:
- user intent
- current tenant and role
- current entity or record context
- relevant mode or workspace lens
- requested action or question
- structured response blocks
- approval requirements
- safe next actions

That contract lets the same intelligence power web chat, PWA chat, and future voice surfaces without redefining the orchestration model.
