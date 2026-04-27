# Weighting and Consensus Documentation

Layer: AI
Scope: Core weighting, dominance rules, consensus thresholds, disagreement handling, and synthesis discipline
Status: Draft v1
Depends On: Titan Zero, AEGIS, specialist cores, orchestration, context packs, audit pipeline
Consumed By: Titan Zero synthesis, orchestration runtime, evaluation layer, future refinement loops
Owner: Agent 04 — AI Platform
Last Updated: 2026-04-15

---

## 1. Purpose

Weighting and consensus define how Titan reconciles multiple reasoning lanes into one governed outcome. They exist so the system does not treat every core as equally important in every situation and does not hide disagreement behind arbitrary synthesis.

This subsystem answers:
- whose reasoning matters most here?
- when is agreement strong enough?
- when should disagreement trigger review?
- how should Titan Zero synthesize competing recommendations?

## 2. Why it exists

A multi-core architecture without weighting and consensus quickly becomes performative rather than useful.

Without weighting and consensus:
- “multi-core” becomes random prompt averaging
- strong domain reasoning can be diluted by weak but eloquent output
- disagreement gets hidden
- governance thresholds become inconsistent
- high-risk requests may sound coherent without actually converging

Weighting and consensus make multi-core reasoning operational instead of decorative.

## 3. Core responsibilities

- assign context-sensitive influence to participating cores
- define dominance rules for different domains
- measure whether outputs converge enough to synthesize safely
- expose when disagreement remains material
- support escalation when consensus is too weak for the risk level
- preserve auditability of why one line of reasoning won more influence

## 4. Weighting principles

### Explicit, not accidental
Weights should be declared or derivable from policy, not emergent from arbitrary prompt order.

### Context-sensitive
Different tasks justify different dominant cores.

### Auditable
The runtime should be able to show which cores mattered and why.

### Bounded by governance
Weights cannot override AEGIS denials or policy constraints.

### Evolvable
Weights may improve through outcome-based evaluation, but only through controlled refinement.

## 5. Core dominance examples

### Logic-dominant scenarios
- scheduling
- dispatch reasoning
- workflow transitions
- dependency checking
- operational contradiction review

### Finance-dominant scenarios
- invoicing
- pricing
- payment collection strategy
- payroll review
- margin or cost tradeoffs

### Creator-dominant scenarios
- communication framing
- UX explanation
- presentation polish
- customer-facing drafts
- narrative compression

### Entropy-sensitive scenarios
Entropy may not dominate, but it may significantly raise review or escalation thresholds in:
- uncertain operations
- anomalous patterns
- low-confidence or contradictory data
- risky cross-domain proposals

## 6. Weighting models

### Fixed template weights
Predefined by task family or domain.

Useful when:
- the task is common
- the risk pattern is stable
- predictability is more important than nuance

### Policy-derived weights
Chosen based on domain, role, risk, tenant preference, or workflow state.

Useful when:
- different tenants have different priorities
- context materially changes what matters most

### Outcome-refined weights
Adjusted gradually through audited evaluation and business outcomes.

Useful when:
- the platform has enough feedback
- governance approves learning-based refinement
- weights remain explainable

## 7. Consensus types

Consensus is not a single yes/no condition. Titan may need multiple kinds of consensus.

### Action consensus
Do the cores broadly agree on what should happen?

### Risk consensus
Do the cores broadly agree on how risky the action is?

### Confidence consensus
Are confidence signals aligned enough to proceed?

### Evidence consensus
Are the cores relying on the same core facts or provenance?

### Presentation consensus
Even if action is agreed, is the outward framing also acceptable?

## 8. Safe synthesis conditions

Titan Zero may synthesize directly when:
- dominant cores agree
- critiques are minor
- confidence is sufficient for the risk band
- AEGIS has not raised blocking concerns
- no unresolved cross-domain contradiction remains

This is the normal happy path.

## 9. Review or escalation conditions

Titan Zero should not synthesize as if all is fine when:
- dominant cores recommend materially different actions
- confidence is low for the task’s risk level
- finance and logic disagree on operationally or commercially significant outcomes
- entropy flags unresolved uncertainty
- evidence is weak or conflicting
- AEGIS requires confirmation or review

## 10. Consensus thresholds

Consensus should vary by risk.

### Low-risk work
Smaller disagreement can still be acceptable.

### Medium-risk work
Consensus should be stronger, especially if customer-facing or workflow-changing.

### High-risk work
Consensus must be high, and disagreement should often trigger confirmation or review instead of auto-synthesis.

## 11. Role of Titan Zero

Titan Zero owns the final synthesis decision.
Weighting and consensus are the discipline Titan Zero uses to justify that synthesis.

Titan Zero should be able to answer:
- which cores participated?
- which one dominated?
- what critiques were considered?
- why was synthesis considered safe?
- why was escalation required?

## 12. Role of AEGIS

AEGIS constrains consensus outcomes.

Even if strong consensus exists, AEGIS may still:
- require review
- force draft-only mode
- deny execution
- block a capability or route
- raise safe mode

Consensus is not permission. It is reasoning alignment.

## 13. Consensus outputs

A consensus process should yield a structured result such as:

- dominant core(s)
- supporting cores
- critique summary
- agreement strength
- unresolved conflicts
- synthesis recommendation
- escalation requirement
- audit notes

This is more useful than a vague internal “confidence” number alone.

## 14. Failure modes

### Dominance drift
One core dominates too often, even in unsuitable domains.

### Hidden disagreement
The synthesis sounds unified while masking real conflict.

### Over-escalation
The system becomes overly cautious and inefficient.

### Under-escalation
High-stakes disagreement gets flattened into one smooth answer.

### Unexplainable weights
The platform cannot explain why one core mattered more.

## 15. Implementation notes

Weighting and consensus should be implemented as explicit orchestration/evaluation components with:
- domain templates
- policy-derived weighting rules
- convergence checks
- critique aggregation
- structured consensus output
- audit references

They should not live as invisible prompt tricks.

## 16. Summary

Weighting and consensus give Titan’s multi-core reasoning a governed method for deciding whose reasoning matters most, when agreement is strong enough to synthesize, and when disagreement should trigger review. Titan Zero uses this discipline to remain authoritative without pretending every core always agrees.

    ## 17. Domain-specific consensus templates

    Consensus templates should exist for recurring task families.

### Good candidates
- dispatch and scheduling
- quoting and invoicing
- payment recovery
- site/job memory review
- customer communication
- module/tool invocation proposals

Template-based weighting improves repeatability and gives evaluation a clearer target for refinement.

    ## 18. Testability and replay

    Consensus behavior should be replayable with the same:
- context pack
- participating cores
- weights
- critique flow
- governance state

That allows the team to inspect why a synthesis happened and whether an escalation should have happened instead.

