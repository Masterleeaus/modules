# 13. Agent Tooling and Execution Contracts

## Purpose
If the system is to become AI-controlled without becoming unsafe, every AI-capable action needs a formal execution contract.

## Principle
An AI should never depend on scraping arbitrary UI state to do real work. It should operate through declared tools, explicit payload contracts, and governed execution paths.

## Tool Contract
Each AI-exposed tool should define:
- tool name
- purpose
- input schema
- output schema
- allowed roles
- affected records
- emitted signals
- approval requirement
- idempotency strategy

## Recommended Tool Families

### CRUD tools
- create quote
- update visit status
- add site note
- assign worker

### Query tools
- fetch today jobs
- list unpaid invoices
- show site access details

### Communication tools
- draft email
- send reminder SMS
- publish WhatsApp template message

### AI/meta tools
- summarize record
- classify issue
- propose next actions
- generate checklist from template

## Thin Controller Rule
Controllers should not hold business logic. They should hand off to actions/services, aligning with the Laravel guidance around cleaner controllers, separation of validation, and reusable service/action patterns fileciteturn1file2.

## DTO / Payload Rule
Agent tools should accept normalized payloads rather than depending on one specific web request shape. This keeps tools reusable across chat, API, web, queue, and node contexts, echoing the DTO and action separation pattern in the uploaded Laravel guidance fileciteturn1file2.

## Execution States
Suggested lifecycle:
- proposed
- validated
- awaiting_approval
- approved
- executed
- failed
- rolled_back

## Approval Types
- auto-approved safe action
- user-approved
- manager-approved
- super-admin approved
- blocked

## Tool Output Doctrine
Tools should return structured results, not just free text.

Recommended fields:
- success boolean
- canonical record IDs
- summary
- warnings
- emitted signals
- next recommended actions

## Audit Requirements
Every tool run should log:
- who initiated it
- which AI persona/system initiated it
- company scope
- input summary
- output summary
- approval path
- final status

## Channel-Aware Tools
Communication tools should declare channel compatibility explicitly.

Example:
- email only
- WhatsApp + SMS
- internal chat only
- portal notification only

## Package Awareness
Not every tenant should have access to every tool.
Tool access should also respect:
- plan/package
- module entitlement
- channel entitlement
- automation tier

## Tool Discovery
Modules should be able to declare tool manifests, consistent with your module checklist’s `ai_tools.json` direction fileciteturn0file0.

## Avoid These Anti-Patterns
- AI calls raw controllers directly without scope enforcement
- AI mutates records through UI hacks
- AI tools lack idempotency keys
- AI uses one tool for too many unrelated jobs
- tools return plain text without machine-readable results

## Build Sequence
1. registry of allowed tools
2. tool schemas
3. execution service
4. proposal/approval wrapper
5. audit log integration
6. module-level tool manifests

## Outcome
Once agent tooling is formalized, the AI stops behaving like an improvising macro layer and starts behaving like a governed operating substrate.
