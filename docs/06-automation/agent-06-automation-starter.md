# Agent 06 Starter Kit — Automation Engines

## Mission

Define the system engines that execute recurring, timed, reactive, and recovery behavior.

## Owns

- lifecycle engine
- reminder engine
- escalation engine
- recovery engine
- retry strategy
- idempotency
- dead letters
- approvals in runtime execution
- engine coordination patterns

## Primary outputs

- automation-engines.md
- lifecycle-engine.md
- reminder-engine.md
- escalation-engine.md
- recovery-engine.md
- retry-strategy.md
- idempotency.md
- dead-letter-queues.md
- approval-runtime.md

## Key questions to answer

- What is an automation engine versus a workflow?
- How are retries and idempotency enforced?
- How are failures quarantined?
- Where do approvals interrupt engine execution?

## Required source alignment

- automation engine blueprint
- signal and governance doctrine
- lifecycle references from module manifests

## Do not do

- blur workflow-state modeling into this layer
- hide reliability patterns inside general platform docs

## Definition of done

The engine layer should clearly explain how Titan performs automated runtime work safely and repeatably.
