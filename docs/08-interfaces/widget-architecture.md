# Titan Zero Documentation

Layer: Interfaces + Surfaces
Scope: Widget architecture, reusable card systems, state blocks, and structured output components across Filament, chat, and PWA shells
Status: Draft v1
Depends On: Core Platform, Modules + Extensions, AI, Communications, PWA + Nodes, Dashboard System
Consumed By: Command Centre, Money, Field Worker, Customer Portal, conversational workspace, future package surfaces
Owner: Agent 08
Last Updated: 2026-04-15

---

## 1. Purpose

Define the widget architecture for Titan so every panel, PWA, and chat surface can render business state through a shared component grammar instead of inventing one-off cards for each module.

## 2. Why it exists

Titan needs many interface surfaces but should still feel like one operating system. Widgets are the bridge between raw module data and user-facing understanding. They convert status, metrics, approvals, alerts, lists, maps, timelines, and AI recommendations into repeatable interface units that can be placed in dashboards, sidebars, command canvases, and conversational responses.

## 3. Core responsibilities

- provide a shared visual vocabulary for state, actions, summaries, and exceptions
- allow the same business information to appear in dashboard, chat, and PWA contexts without redefining the domain
- separate rendering concerns from module logic and action execution
- support dense operator views and lightweight mobile views using the same underlying data contract
- make AI-generated structured output renderable without custom screen building every time

## 4. Widget doctrine

A Titan widget is not a mini-module and not a business engine. It is a presentation block that consumes approved data, known actions, and stable contracts. Widgets may summarize, compare, visualize, filter, or route the user to a deeper surface, but they must not become the place where domain rules live.

### Rules

- module logic stays in modules, actions, services, policies, and workflows
- widgets consume view models, DTOs, resource data, or approved AI output envelopes
- widgets may trigger actions, but the action logic must exist outside the widget
- widgets must degrade by density: desktop dense, mobile focused, chat compact
- widgets must be composable into dashboards, panes, and canvas layouts

## 5. Widget families

### 5.1 Status widgets

Used for a single entity or process state.

Examples:
- job status
- worker availability
- invoice state
- campaign health
- sync queue state
- AI provider availability

### 5.2 Metric widgets

Used for numerical summary.

Examples:
- jobs due today
- money at risk
- overdue invoices
- open exceptions
- average response time
- unapproved automations

### 5.3 Queue widgets

Used for pending work.

Examples:
- approvals queue
- dispatch queue
- jobs needing assignment
- invoices overdue follow-up
- site issues awaiting triage

### 5.4 Alert widgets

Used for exceptions and warnings.

Examples:
- failed syncs
- policy denials
- late worker arrivals
- missing proof of service
- financial anomalies

### 5.5 Action widgets

Used for quick execution entry.

Examples:
- create quote
- schedule visit
- approve invoice
- contact customer
- dispatch nearest worker

### 5.6 Insight widgets

Used for AI or analytical interpretation.

Examples:
- route optimization suggestion
- margin risk explanation
- staffing warning
- follow-up recommendation
- summary of urgent changes

### 5.7 Timeline widgets

Used for event sequencing.

Examples:
- job lifecycle
- customer history
- signal audit
- payment chase history
- workflow transition chain

### 5.8 Relationship widgets

Used to show connected entities.

Examples:
- customer → sites → jobs
- agreement → visits → invoices
- worker → shifts → timesheets
- campaign → leads → quotes

## 6. Shared widget schema

Every reusable widget should be describable with a stable interface contract.

Recommended fields:

- `id`
- `type`
- `title`
- `subtitle`
- `state`
- `priority`
- `icon`
- `metrics`
- `badges`
- `body`
- `actions`
- `links`
- `context`
- `updated_at`
- `permissions`
- `density`
- `surface_support`

This schema allows widgets to be rendered by:
- Filament dashboard pages
- command-centre layouts
- chat result blocks
- mobile PWA panes
- side panels
- notifications previews

## 7. Density model

### Dense

Used in desktop admin and command surfaces.

Characteristics:
- multiple metrics visible
- nested actions
- badges and secondary data
- comparison columns
- filters or tabs

### Standard

Used in normal dashboard and panel contexts.

Characteristics:
- one primary metric or state
- one to three actions
- moderate detail

### Compact

Used in mobile and side panes.

Characteristics:
- single line summary
- one tap action
- fewer labels
- progressive disclosure

### Conversational

Used in chat-first workspace.

Characteristics:
- readable in thread flow
- converts cards into suggested actions
- may collapse details behind expanders
- keeps language attached to data block

## 8. Surface mapping

### Filament dashboards

Widgets are primary building blocks for overview and drill-in surfaces.

### Chat workspace

Widgets become structured response blocks after a Titan Zero answer, preserving machine-readable action targets.

### PWA apps

Widgets become mobile cards, stacked summaries, alerts, and action trays.

### Portal and customer surfaces

Widgets must be simpler, safer, and more approval-oriented.

## 9. Widget-to-action contract

Widgets may expose actions, but action execution must follow one shared pattern:

1. widget emits intent
2. intent resolves to route, action, or workflow trigger
3. policy and permission checks occur outside widget
4. execution result returns as state refresh, redirect, modal, or chat update

This prevents business rules from being trapped in UI callbacks.

## 10. Widget composition patterns

### Overview strip

A row of metrics and alerts.

### Stack

A vertical set of related cards.

### Split

Summary left, queue or timeline right.

### Focus panel

One large widget plus related child blocks.

### Command canvas

Conversation, widgets, and forms in one coordinated work zone.

## 11. Module integration rules

Modules should expose widget-ready view models or transformers rather than raw tables.

Preferred flow:
- module action or service gathers domain data
- transformer or view model shapes data for surface needs
- widget renderer consumes that shape

Avoid:
- database querying directly inside widget classes
- domain rule branching in table or card callbacks
- duplicated calculations across dashboard and chat surfaces

## 12. AI integration rules

AI output may request widgets, but AI must not invent domain semantics.

AI may:
- select from known widget types
- populate supported fields from trusted context packs
- recommend density based on surface
- attach allowed actions from registered tools

AI may not:
- create unregistered action types
- bypass permissions
- fabricate business states not present in system data

## 13. Accessibility and usability rules

- state must never be color-only; use labels and icons too
- primary action must be obvious
- compact widgets must keep touch-safe spacing
- alert widgets should communicate urgency without noise
- data widgets should display source freshness where possible
- user-facing widgets should avoid internal jargon

## 14. Example suite mapping

### Command Centre
- KPI strip
- approvals queue
- urgent issues
- live workforce state
- AI recommendation panel

### Field Worker
- today list
- next job card
- access instructions
- checklist progress
- proof capture state

### Money
- overdue invoices
- payment recovery queue
- cashflow snapshot
- margin risk card
- unbilled work alert

### Portal
- next booking
- quote awaiting approval
- invoice due
- service history timeline
- contact and support actions

## 15. Build guidance

Start with a small canonical widget set and reuse it everywhere.

Suggested first set:
- status card
- metric card
- queue card
- alert card
- action card
- timeline card
- insight card

These seven can cover most early Titan needs across dashboard, chat, and PWA surfaces.

## 16. Done criteria

This document is complete when a developer can design widgets once and reuse them across Filament panels, conversational canvases, and installable PWAs without moving business logic into the interface layer.


## 11. Cross-surface widget contract

A Titan widget should be portable across at least three contexts:

- Filament dashboard or page
- chat structured block
- PWA summary/detail surface

To make that possible, widgets should be backed by a small stable contract:

- title
- state/metric payload
- optional icon/visual hint
- primary action list
- risk/severity marker
- entity links/context ids
- freshness timestamp

## 12. CMS and manifest overlap

Where a module exposes `cms_manifest.json` or PWA-facing widget definitions, the widget library should align to those declared surfaces instead of inventing parallel card types. The same invoice/job/booking widget family should be renderable in dashboards, website/PWA surfaces, and assistant results.

## 13. Operational widget families to prioritize

For this system, the highest-value reusable widgets are:

- status cards
- queue cards
- approval cards
- money-at-risk cards
- schedule/day-plan cards
- worker/site memory cards
- offline/sync health cards
- communication delivery cards


## 13. Shared widget contract

A reusable Titan widget should be defined by contract, not by one screen implementation. A good cross-surface widget contract includes:
- entity or aggregate type
- canonical data payload
- freshness or timestamp information
- allowed actions
- risk or status markers
- optional AI summary region
- mobile reduction rules

This lets the same widget family render inside Filament dashboards, conversational canvases, and PWA cards without duplicating business logic.

## 14. Command, field, and money widget sets

Recommended first widget families:

### Command
- exception card
- dispatch queue card
- approval card
- live KPI strip
- node/health status card

### Field
- today job card
- checklist block
- proof-of-service card
- site-memory card
- sync-state card

### Money
- invoice card
- payment session card
- overdue risk card
- margin summary card
- approval-gated action card

## 15. Widget anti-patterns

Avoid:
- embedding domain rules directly in widget callbacks
- creating different truth models per surface
- hiding stale or offline state
- inventing one-off card grammars for each module
