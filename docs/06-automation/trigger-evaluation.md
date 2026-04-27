# Titan Zero Documentation

Layer: Automation
Scope: How runtime triggers, conditions, timers, and windows create automation work safely
Status: Draft v1
Depends On: Automation engines, scheduling, workflows, signals, governance, runtime state
Consumed By: Titan Zero, AEGIS, lifecycle engine, reminder engine, escalation engine, module actions
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan decides that automation should start, continue, pause, or stop.

This doc covers the **evaluation boundary** before execution:

- triggers
- conditions
- timers
- recurrence
- windows
- overlap blocking
- suppression

## 2. Why it exists

The full engine blueprint gives the automation and scheduling layers explicit folders for **Triggers**, **Conditions**, **Timers**, **Windows**, and **OverlapControl**. Laravel’s queue/scheduler material also highlights timed tasks, blocking, overlap control, and hooks as core runtime concerns. fileciteturn3file14

Without a trigger-evaluation layer:

- jobs fire because a queue message exists, not because policy still allows them
- reminders run outside legal time windows
- escalations race with recovery or approval pause states
- the same trigger can spawn duplicate work
- workflow state leaks into automation ownership

## 3. Trigger evaluation versus execution

Trigger evaluation answers:

- should runtime work exist?
- why now?
- under what constraints?
- who owns the next engine handoff?

Execution answers:

- run the action
- defer it
- retry it
- escalate it
- quarantine it

Keep these separate.

## 4. Trigger categories

### Event triggers

Examples:

- quote sent
- invoice overdue
- booking confirmed
- process entered waiting stage
- approval granted

These usually come from signals, inbox relays, or module events.

### Time triggers

Examples:

- 24 hours before appointment
- 3 days after quote with no response
- 15 minutes after failed send for retry
- every Monday during a maintenance window

These usually come from scheduling and recurrence rules.

### State triggers

Examples:

- process still awaiting approval
- item remains unpaid past SLA
- dead-letter item marked ready for re-drive

These depend on runtime state, not just one event.

### Composite triggers

Examples:

- invoice overdue **and** customer not opted out **and** business hours open
- booking missed **and** retry budget exhausted **and** escalation channel enabled

## 5. Trigger evaluation pipeline

Suggested order:

1. receive candidate trigger input
2. normalize to a canonical trigger record
3. resolve tenant and authority mode
4. resolve current process/runtime state
5. apply conditions and suppressions
6. apply time windows and overlap rules
7. check approval/governance interruption rules
8. emit execution work or suppress with reason code

## 6. Conditions

Conditions are boolean checks that determine whether a trigger may proceed.

Typical condition inputs:

- company settings
- feature flags
- consent state
- module manifest support
- lifecycle stage
- approval state
- retry budget
- channel health

Conditions should be explicit and auditable.

## 7. Timers and recurrence

Timers define *when* a trigger becomes eligible.

Examples:

- run after 10 minutes
- run at 9:00 local tenant time
- run every weekday
- run only once per stage entry

Recurrence rules should never imply permission by themselves. They only make a trigger eligible for evaluation.

## 8. Windows

A window defines when an otherwise-valid trigger is allowed to proceed.

Common windows:

- business hours only
- weekday only
- no-send quiet hours
- maintenance window
- grace period after state change

If a trigger is valid but outside its window, it should move to **deferred/pending_window**, not fail.

## 9. Overlap control

Overlap control prevents duplicate or conflicting work from running at the same time.

Examples:

- do not send two reminders for the same invoice simultaneously
- do not escalate while approval is still pending
- do not recover and retry the same delivery in parallel

Overlap keys typically use:

- process record ID
- entity ID
- action type
- tenant ID
- channel target

## 10. Suppression rules

A valid trigger can still be suppressed.

Common suppressions:

- user already responded
- job already completed
- invoice already paid
- communication opt-out active
- approval pause active
- authority mode forbids cross-app action

Suppression should produce a reason code, not disappear silently.

## 11. Trigger ownership by engine

### Lifecycle Engine

Owns stage-based trigger timing.

### Reminder Engine

Owns cadence triggers for nudges and follow-ups.

### Escalation Engine

Owns breach and timeout triggers.

### Recovery Engine

Owns re-drive and resume triggers after failure or quarantine release.

The trigger layer should route work to the correct owner instead of letting every engine poll everything.

## 12. Governance and approval interruptions

Trigger eligibility does not guarantee execution.

A trigger can be blocked because:

- risk class increased
- approval required
- tenant authority mode forbids action
- manifest/capability is missing
- AI proposed action lacks authorization

Modules and engines expose manifests precisely so lifecycle actions, automation triggers, and signals can be governed consistently. fileciteturn3file0 fileciteturn3file7

## 13. Required records

Each evaluated trigger should capture:

- trigger type
- source event/timer
- evaluation timestamp
- tenant
- evaluated conditions
- suppression reason if blocked
- window result
- overlap result
- downstream owner engine
- resulting execution/process ID if created

## 14. Anti-patterns

### Poll everything forever

Bad:
- every engine scans the same tables independently

Good:
- one trigger-evaluation boundary creates owned runtime work

### Scheduler equals business logic

Bad:
- cron expression directly decides business action

Good:
- scheduler only surfaces eligible time triggers; policy evaluation still happens after that

### Silent suppression

Bad:
- reminder just never sends and leaves no trace

Good:
- reminder suppressed with explicit reason code and state entry

## 15. Outcome

Trigger evaluation is the guardrail between “something happened” and “Titan should act.” It ensures automation begins only when timing, policy, state, and ownership all agree, keeping engine execution deterministic, rate-limited, and explainable.
