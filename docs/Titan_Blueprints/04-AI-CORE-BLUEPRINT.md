# AI Core Blueprint

Status: Canonical draft
Layer: AI core

## Principle

AI is a first-class system layer, not a sidecar.

## AI Tree

```text
app/Platform/Ai/
├─ Core/
│  ├─ TitanZero/
│  ├─ Aegis/
│  ├─ Equilibrium/
│  ├─ Micro/
│  ├─ Macro/
│  ├─ Logic/
│  ├─ Creator/
│  ├─ Finance/
│  ├─ Entropy/
│  └─ Sentry/
├─ Orchestration/
│  ├─ Consensus/
│  ├─ Arbitration/
│  ├─ Weighting/
│  ├─ Critique/
│  └─ Synthesis/
├─ Context/
│  ├─ ContextPacks/
│  ├─ EnvelopeBuilders/
│  ├─ MemoryResolvers/
│  ├─ ToolContext/
│  └─ Retrieval/
├─ Memory/
│  ├─ Session/
│  ├─ User/
│  ├─ Tenant/
│  ├─ Site/
│  ├─ Job/
│  ├─ Working/
│  └─ RecallPolicies/
├─ Routing/
│  ├─ LocalModels/
│  ├─ ExternalModels/
│  ├─ Delegation/
│  ├─ CostPolicy/
│  ├─ PrivacyPolicy/
│  └─ LatencyPolicy/
├─ Tooling/
│  ├─ Registries/
│  ├─ Executors/
│  ├─ Adapters/
│  ├─ Validators/
│  └─ ResultNormalizers/
├─ Governance/
│  ├─ Proposals/
│  ├─ Approvals/
│  ├─ Denials/
│  ├─ SafeModes/
│  ├─ Constraints/
│  └─ RiskScoring/
├─ Voice/
│  ├─ Realtime/
│  ├─ Streams/
│  ├─ Interrupts/
│  ├─ Confirmations/
│  └─ DeviceAdapters/
├─ Evaluation/
│  ├─ Judges/
│  ├─ HallucinationChecks/
│  ├─ ConsistencyChecks/
│  ├─ PolicyChecks/
│  └─ EvidenceScoring/
├─ Training/
│  ├─ Refinement/
│  ├─ Feedback/
│  ├─ OutcomeSignals/
│  ├─ Deltas/
│  └─ Metrics/
└─ Support/
```

## Core Responsibilities

### Titan Zero
Primary user-facing intelligence and synthesizer.

### AEGIS
Governance, policy, and authority checks.

### Specialist cores
Logic, creator, finance, entropy, and other persona layers.

### Memory
User, tenant, site, job, session, and working memory layers.

### Routing
Choose local vs external model under privacy, cost, and latency policy.

### Tooling
Register and execute module tools safely.

### Governance
Proposal, approval, denial, risk scoring, safe modes.

### Evaluation
Judges, evidence scoring, consistency and hallucination checks.

### Training
Refinement loops based on approvals, denials, outcomes, and feedback.

## AI Presence Outside `app/Platform/Ai`

```text
app/Models/Ai/
app/Actions/Ai/
app/Services/Ai/
app/Jobs/Ai/
app/Events/Ai/
app/Listeners/Ai/
app/Http/Controllers/Api/Ai/
app/Notifications/Ai/
```

## AI Contracts Modules Must Expose

- `ai_tools.json`
- signal manifests
- lifecycle manifests
- context envelope compatibility
