# Titan Workflow Engine

## Purpose

The Titan Workflow Engine governs formal state progression
for business entities across the platform.

It models structured multi-step processes such as:

- lead to quote
- quote to booking
- booking to job
- job to invoice
- invoice to follow-up
- approval-driven admin flows

It ensures transitions are legal, visible, and recoverable.

## Core Location

`app/Platform/Workflows/`

Primary sublayers:

- `Definitions/`
- `StateMachines/`
- `StepHandlers/`
- `Guards/`
- `Conditions/`
- `Transitions/`
- `Approvals/`
- `Templates/`
- `Metrics/`
- `Support/`

## Workflow Philosophy

A workflow is not just status text.

It is a governed transition system with:

- explicit states
- allowed transitions
- guard conditions
- side effects
- approval gates
- metrics
- recovery paths

This keeps lifecycle movement consistent across UI, API, AI, and automation.

## Definitions

Workflow definitions describe:

- entity type
- available states
- allowed transitions
- required guards
- emitted signals
- approval requirements
- rollback rules

Definitions should be declarative and versionable.

## State Machines

State machines are the execution model for transitions.

Examples:

- draft → planned
- planned → scheduled
- scheduled → dispatched
- dispatched → in_progress
- in_progress → completed
- completed → invoiced
- invoiced → closed

Illegal jumps must be rejected.

## Step Handlers

Each workflow step may trigger handlers such as:

- create checklist set
- reserve schedule slot
- notify technician
- issue invoice draft
- request proof of service
- enqueue follow-up campaign

Handlers should call actions/services, not embed business logic in controllers.

## Guards

Guards prevent invalid transitions.

Examples:

- cannot dispatch without assignee
- cannot complete without required checklist
- cannot invoice without billable state
- cannot close while payment remains outstanding

Guards are mandatory for operational integrity.

## Approval Gates

Some transitions require approval before moving forward.

Examples:

- manual override to completed
- write-off before close
- discount beyond threshold
- schedule changes inside protected windows

Approval gating integrates with AEGIS and Sentinel.

## Signals

Workflow transitions emit signals such as:

- `job.scheduled`
- `job.dispatched`
- `job.completed`
- `invoice.generated`

This allows downstream automation and communications to react consistently.

## Templates

Workflow templates allow reuse across domains.

Examples:

- service job workflow
- quote workflow
- invoice workflow
- complaint resolution workflow
- onboarding workflow

Templates speed module development and keep semantics aligned.

## Metrics

Workflow metrics should track:

- state dwell time
- stuck transitions
- rejection rates
- approval delays
- average completion time
- exception frequency

This turns workflows into measurable system behavior.

## Recovery

Workflow engine should support:

- replay after failure
- resume after approval
- rollback where allowed
- exception routing
- stuck-state repair tools

Recovery is required for real operations.

## Lifecycle Manifest Integration

Modules can declare workflow-compatible states using:

`lifecycle_manifest.json`

Workflow Engine uses this to align module state contracts with runtime transitions.

## Workflow Engine Responsibilities

Owns:

- state machine enforcement
- transition legality
- guard evaluation
- approval gating
- workflow templates
- transition metrics
- recovery controls
- signal emission on progression

This converts module statuses into governed process architecture.
