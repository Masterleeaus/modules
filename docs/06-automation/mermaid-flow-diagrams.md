# Titan Zero Documentation

Layer: Automation
Scope: Mermaid diagrams for core automation runtime flows, approval pauses, retries, dead letters, replay, and cross-engine handoff
Status: Draft v1
Depends On: automation-engines.md, lifecycle-engine.md, approval-runtime.md, retry-strategy.md, dead-letter-queues.md, process-record-integration.md, decision-envelopes.md, outbox-inbox-relays.md, titan-governance-flow-mapping.md
Consumed By: Developers, operators, support teams, implementation agents, onboarding docs, runbook authors
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Provide reusable Mermaid diagrams that visualize the automation runtime in a consistent way.

## 2. Why it exists

The automation docs explain responsibilities in detail, but diagrams are still needed for:

- fast onboarding
- architecture reviews
- operator training
- support handoffs
- GitHub issue prompts
- implementation validation

Without diagrams:

- engine boundaries blur
- approval pauses are misread as synchronous UI actions
- retries and dead letters get hidden inside queue folklore
- ProcessRecord usage is forgotten
- AI governance handoffs look magical instead of explicit

## 3. Diagram rules

All automation diagrams should follow these rules:

- show runtime pauses explicitly
- show governance before execution
- show ProcessRecord writes where state becomes durable
- show idempotency/lock checks before side effects
- show dead-letter as a controlled terminal branch
- show replay as a new governed run, not a raw requeue
- distinguish proposal, approval, execution, and delivery

## 4. Diagram A: governed signal-to-action flow

```mermaid
flowchart TD
    A[Module Event or AI Proposal] --> B[Signal Intake]
    B --> C[Schema Validation]
    C --> D[AEGIS Governance Check]
    D --> E[Sentinel Domain Readiness Check]
    E --> F[Decision Envelope]
    F --> G[Trigger Evaluation]
    G --> H[Idempotency / Overlap Check]
    H --> I[ProcessRecord Open or Update]
    I --> J[Engine Runtime Selection]
    J --> K[Module Action Execution]
    K --> L[Outbox / Inbox Relay]
    L --> M[Channel or Downstream Delivery]
    M --> N[Audit / Telemetry]
```

### What it shows

- no action executes before governance
- decision envelopes are first-class runtime inputs
- ProcessRecord becomes the durable state spine
- delivery is downstream from action execution, not the action itself

## 5. Diagram B: approval-sensitive runtime pause

```mermaid
flowchart TD
    A[Incoming Signal or Proposal] --> B[Governance + Domain Checks]
    B --> C{Approval Required?}
    C -- No --> D[Execute Engine Path]
    C -- Yes --> E[Create Approval Record]
    E --> F[Pause Runtime]
    F --> G[Operator Reviews]
    G --> H{Approve?}
    H -- Approve --> I[Resume Runtime]
    I --> D
    H -- Deny --> J[Mark Denied]
    J --> K[Audit + Notify]
    H -- Expire --> L[Escalate or Cancel]
```

### What it shows

- approval is a durable state, not a modal dialog
- pause/resume is runtime behavior
- deny/expire are explicit terminal branches

## 6. Diagram C: reminder to escalation handoff

```mermaid
flowchart TD
    A[Lifecycle Stage Reached] --> B[Reminder Window Created]
    B --> C[Reminder Sent]
    C --> D{Acknowledged in Time?}
    D -- Yes --> E[Record Success]
    D -- No --> F[Trigger Breach Detected]
    F --> G[Escalation Engine Opens Run]
    G --> H[Route to Manager / Ops Inbox]
    H --> I[Communications Layer Sends Escalation]
    I --> J[ProcessRecord Attention Required]
    J --> K[Audit + Telemetry]
```

## 7. Diagram D: retry and dead-letter path

```mermaid
flowchart TD
    A[Action Execution Attempt] --> B{Succeeded?}
    B -- Yes --> C[Mark Complete]
    B -- No --> D{Recoverable Error?}
    D -- No --> E[Dead Letter Immediately]
    D -- Yes --> F[Retry Policy Evaluates]
    F --> G{Attempts Remaining?}
    G -- Yes --> H[Schedule Next Retry]
    H --> A
    G -- No --> I[Quarantine to Dead Letter]
    E --> J[Escalation Optional]
    I --> J
    J --> K[Audit + Operator Visibility]
```

### What it shows

- retry policy is not the same thing as queue retry defaults
- dead letter is explicit quarantine after policy exhaustion
- operators should see the failure state clearly

## 8. Diagram E: recovery and replay flow

```mermaid
flowchart TD
    A[Dead Letter Entry] --> B[Recovery Eligibility Check]
    B --> C{Replay Safe?}
    C -- No --> D[Hold for Manual Investigation]
    C -- Yes --> E[Rebuild Context]
    E --> F[Reconstruct Decision Envelope]
    F --> G[Create New Governed Run]
    G --> H[Re-check Idempotency and Locks]
    H --> I[Execute Again]
    I --> J{Succeeded?}
    J -- Yes --> K[Resolve Dead Letter]
    J -- No --> L[Return to Quarantine or Escalate]
```

## 9. Diagram F: outbox/inbox relay boundary

```mermaid
flowchart LR
    A[Module Action] --> B[Write Outbox Row]
    B --> C[Relay Worker]
    C --> D[Inbox Entry / Channel Queue / API Adapter]
    D --> E[Delivery Attempt]
    E --> F[Delivery Result]
    F --> G[Audit + Telemetry]
```

### What it shows

- action execution and message delivery are separated
- relay workers move durable payloads across boundaries
- delivery results must return to telemetry and audit

## 10. Diagram G: ProcessRecord-centered automation

```mermaid
flowchart TD
    A[Signal / Proposal] --> B[Find or Open ProcessRecord]
    B --> C[Attach Run Metadata]
    C --> D[Advance Lifecycle / Reminder / Escalation / Recovery State]
    D --> E[Store Current Runtime Position]
    E --> F[Emit Next Handoff or Complete]
    F --> G[Close or Keep Open Based on Process State]
```

## 11. Diagram H: Titan Zero to governed automation

```mermaid
flowchart TD
    A[Titan Zero Proposal] --> B[AEGIS Policy / Risk Review]
    B --> C[Sentinel Domain Readiness]
    C --> D[Decision Envelope]
    D --> E{Approval Needed?}
    E -- Yes --> F[Approval Runtime]
    E -- No --> G[Automation Runtime]
    F --> G
    G --> H[Module Action]
    H --> I[Channels / APIs / State Updates]
    I --> J[Audit + Learning Signals]
```

## 12. Recommended usage

Use these diagrams in:

- module docs
- GitHub implementation prompts
- ops runbooks
- support/debug docs
- onboarding packs
- architecture review notes

## 13. Implementation notes

When converting diagrams to real code, verify that:

- the named tables and records actually exist
- approval rows are durable and resumable
- retry policy is configurable per engine family
- dead-letter payloads keep causation metadata
- ProcessRecord is updated by engine transitions, not only by modules
- delivery telemetry returns from channels to the runtime layer

## 14. Related docs

- `automation-engines.md`
- `approval-runtime.md`
- `retry-strategy.md`
- `dead-letter-queues.md`
- `process-record-integration.md`
- `decision-envelopes.md`
- `outbox-inbox-relays.md`
- `worked-engine-examples.md`
- `titan-governance-flow-mapping.md`
