# Titan PWA Node Governance Runtime

## Purpose

Defines the enforcement layer that ensures Titan Nodes remain policy-compliant,
permission-safe, replay-deterministic, and approval-gated during autonomous or assisted execution.

Governance guarantees that nodes recommend actions but never execute restricted operations independently.

---

## Responsibilities

Governance runtime enforces:

- permission validation
- approval gating
- tenant isolation
- signal eligibility verification
- workflow transition legality
- automation safety constraints
- replay integrity guarantees

---

## Governance Check Order

Recommended evaluation order:

1. tenant and actor identity validation
2. permission and scope check
3. workflow legality check
4. overlay and policy evaluation
5. approval requirement determination
6. replay and idempotency check
7. final emit, hold, or reject decision

## Governance Outcomes

A governance decision should resolve into one of the following states:

- allowed
- held_for_approval
- rejected
- deferred
- replay_blocked
- overlay_blocked
