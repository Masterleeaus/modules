# Titan Zero Documentation

Layer: Automation
Scope: Retry strategy for engine executions, deferred jobs, deliveries, and runtime actions
Status: Draft
Depends On: automation-engines.md, idempotency.md, recovery-engine.md, queue runtime, signal governance
Consumed By: lifecycle engine, reminder engine, escalation engine, communications layer, module actions
Owner: Agent 06
Last Updated: 2026-04-15

---

## 1. Purpose

Define how Titan retries failed automated work without creating duplicate side effects, runaway loops, or hidden cross-tenant risk.

## 2. Why it exists

Retries are a reliability tool, not a generic “try again” button. In Titan, automation may create reminders, send messages, move lifecycle state, trigger billing follow-up, or call external APIs. Each of those actions needs a formal retry policy so transient failure can be absorbed while unsafe repetition is blocked.

## 3. Core responsibilities

- classify retryable versus non-retryable failures
- apply bounded retry schedules with backoff and jitter where appropriate
- enforce idempotency before each retry attempt
- hand off to recovery or dead-letter flows when retry budget is exhausted
- record retry decisions for audit and observability

## 4. Boundaries

### In scope

- queue-backed retry behavior
- engine-level retry policy
- retry budgets and attempt caps
- retry-aware delivery and action execution
- escalation after repeated failure

### Out of scope

- infinite retries
- silent UI refresh loops
- business approval decisions
- broad workflow state modeling
- custom one-off retry logic embedded inside controllers or widgets

## 5. Architecture

Retry strategy belongs to the automation runtime layer and should be shared across engines.

A standard retry path should be:

1. action fails
2. failure is classified
3. idempotency state is checked
4. retry policy decides delay, budget, and safety
5. next attempt is scheduled or rejected
6. exhausted attempts hand off to recovery, dead-letter, or operator review

Retry policy should support at least:

- immediate short retry for transient infrastructure errors
- exponential backoff for unstable downstream dependencies
- no retry for validation, authorization, or policy denial
- approval-aware pause where work requires human authorization before continuation

## 6. Contracts

Inputs:

- action or engine run identifier
- failure type
- current attempt count
- max retry budget
- idempotency key
- tenant/company identifier
- downstream dependency metadata
- last checkpoint state

Outputs:

- next scheduled retry
- transition to recovery path
- transition to dead-letter queue
- operator escalation event
- structured retry audit entry

## 7. Runtime behavior

Every retry must be treated as a fresh execution attempt under the same safety envelope.

That means:

- re-check idempotency before acting
- re-check approval state if approval gates can expire or change
- re-check overlap/lock conditions if timing matters
- prefer deterministic scheduling over ad hoc sleep loops

Retries should also remain visible to operators. A system that retries invisibly forever creates false confidence and hides degraded automation.

## 8. Failure modes

Retry strategy can fail in its own right when:

- retry budget is too aggressive and causes duplicate pressure
- retry budget is too weak and drops recoverable work
- idempotency records are missing or inconsistent
- external APIs succeed but do not return confirmation cleanly
- the same action is retried from multiple surfaces simultaneously

Mitigations:

- bounded retry caps
- centralized idempotency enforcement
- durable attempt logging
- lock/overlap control
- mandatory handoff to recovery or dead-letter after budget exhaustion

## 9. Dependencies

Upstream:

- automation triggers
- engine execution records
- queue runtime
- signal governance and approval state

Downstream:

- recovery engine
- dead-letter queues
- escalation engine
- communications delivery reporting
- observability and audit surfaces

## 10. Open questions

- Which retry classes should be standardized across all modules?
- Should communications channels each have their own retry adapter beneath the shared policy?
- How should retry budgets differ between internal work and customer-visible outbound messages?

## 11. Implementation notes

Laravel’s queue-oriented patterns make retries a natural infrastructure concern, but Titan needs a stronger platform rule: retries must be explicit, idempotent, bounded, and observable. They should wrap shared actions and services rather than duplicate logic across controllers, Filament callbacks, APIs, or import jobs. The module checklist and engine blueprint both point toward that architecture by pushing core behavior into reusable service/action layers and exposing manifests for signals and lifecycle participation.
