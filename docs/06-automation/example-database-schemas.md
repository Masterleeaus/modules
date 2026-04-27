# Titan Zero Documentation

Layer: Automation
Scope: Example database schemas for automation runtime state, approvals, retries, dead letters, and relay boundaries
Status: Draft v1
Depends On: runtime-state-store.md, process-record-integration.md, approval-runtime.md, dead-letter-queues.md, outbox-inbox-relays.md
Consumed By: Platform engineers, module developers, DB designers, installer/doctor, audit tooling
Owner: Agent 06 — Automation
Last Updated: 2026-04-15

---

## 1. Purpose

Provide concrete starter schema patterns for the automation runtime so teams can implement durable state, approval pauses, retries, dead letters, relays, and replay support without inventing inconsistent tables on each module.

## 2. Why it exists

The automation documents describe behavior, but operations teams still need a durable persistence shape.

Without schema guidance:

- retries get stored in ad hoc queue payloads only
- approval pauses cannot be resumed reliably
- dead-letter data loses causality
- replay cannot reconstruct chain state cleanly
- modules build conflicting runtime tables
- installer and doctor cannot verify automation readiness

This document gives a baseline persistence model for Titan automation.

## 3. Core rule

Automation state should be stored in platform-owned runtime tables, not hidden inside module entities.

Modules keep business truth.
Automation keeps execution truth.
Audit keeps decision truth.

## 4. Canonical schema groups

Recommended groups:

- `process_records`
- `automation_runs`
- `automation_approvals`
- `automation_retries`
- `automation_dead_letters`
- `automation_outbox`
- `automation_inbox`
- `automation_runtime_locks`
- `automation_trigger_logs`
- `automation_transition_logs`

## 5. Example: process_records

Use as the durable parent spine for long-running automation chains.

```sql
CREATE TABLE process_records (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  process_key VARCHAR(120) NOT NULL,
  entity_type VARCHAR(80) NULL,
  entity_id BIGINT UNSIGNED NULL,
  current_stage VARCHAR(80) NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'open',
  decision_envelope_id VARCHAR(120) NULL,
  started_at DATETIME NULL,
  completed_at DATETIME NULL,
  failed_at DATETIME NULL,
  correlation_id VARCHAR(120) NULL,
  causation_id VARCHAR(120) NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_process_company (company_id),
  INDEX idx_process_key (process_key),
  INDEX idx_process_entity (entity_type, entity_id),
  INDEX idx_process_status (status),
  INDEX idx_process_correlation (correlation_id)
);
```

### Notes

- `company_id` is mandatory tenant boundary
- `process_key` is stable, replay-safe, and machine-readable
- `decision_envelope_id` links governance and runtime
- `correlation_id` and `causation_id` preserve lineage across hops

## 6. Example: automation_runs

Use as the execution attempt table below each process.

```sql
CREATE TABLE automation_runs (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  process_record_id BIGINT UNSIGNED NOT NULL,
  engine_family VARCHAR(60) NOT NULL,
  engine_name VARCHAR(80) NOT NULL,
  action_key VARCHAR(120) NOT NULL,
  idempotency_key VARCHAR(180) NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'queued',
  scheduled_for DATETIME NULL,
  started_at DATETIME NULL,
  finished_at DATETIME NULL,
  failed_at DATETIME NULL,
  last_error_code VARCHAR(80) NULL,
  last_error_message TEXT NULL,
  attempt_count INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_runs_company (company_id),
  INDEX idx_runs_process (process_record_id),
  INDEX idx_runs_engine (engine_family, engine_name),
  INDEX idx_runs_status (status),
  UNIQUE KEY uniq_run_idempotency (company_id, idempotency_key)
);
```

### Status suggestions

- queued
- waiting_approval
- delayed
- running
- succeeded
- failed
- quarantined
- cancelled

## 7. Example: automation_approvals

Use to pause and resume runtime work safely.

```sql
CREATE TABLE automation_approvals (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  process_record_id BIGINT UNSIGNED NOT NULL,
  automation_run_id BIGINT UNSIGNED NULL,
  approval_scope VARCHAR(80) NOT NULL,
  approval_mode VARCHAR(80) NOT NULL,
  requested_by_type VARCHAR(80) NULL,
  requested_by_id BIGINT UNSIGNED NULL,
  approved_by BIGINT UNSIGNED NULL,
  denied_by BIGINT UNSIGNED NULL,
  decision VARCHAR(20) NOT NULL DEFAULT 'pending',
  decision_notes TEXT NULL,
  requested_at DATETIME NULL,
  decided_at DATETIME NULL,
  expires_at DATETIME NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_approvals_company (company_id),
  INDEX idx_approvals_process (process_record_id),
  INDEX idx_approvals_decision (decision)
);
```

## 8. Example: automation_retries

Use when attempt policy needs durable retry planning, not only queue backoff.

```sql
CREATE TABLE automation_retries (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  automation_run_id BIGINT UNSIGNED NOT NULL,
  retry_number INT UNSIGNED NOT NULL,
  retry_reason VARCHAR(120) NULL,
  scheduled_for DATETIME NOT NULL,
  executed_at DATETIME NULL,
  result_status VARCHAR(40) NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_retries_company (company_id),
  INDEX idx_retries_run (automation_run_id),
  INDEX idx_retries_scheduled (scheduled_for)
);
```

## 9. Example: automation_dead_letters

Use to quarantine exhausted or unsafe work.

```sql
CREATE TABLE automation_dead_letters (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  process_record_id BIGINT UNSIGNED NULL,
  automation_run_id BIGINT UNSIGNED NULL,
  signal_key VARCHAR(120) NULL,
  action_key VARCHAR(120) NULL,
  failure_code VARCHAR(80) NULL,
  failure_message TEXT NULL,
  payload_json LONGTEXT NULL,
  quarantined_at DATETIME NOT NULL,
  re_driven_at DATETIME NULL,
  resolved_at DATETIME NULL,
  resolution_notes TEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_dead_company (company_id),
  INDEX idx_dead_process (process_record_id),
  INDEX idx_dead_action (action_key),
  INDEX idx_dead_quarantined (quarantined_at)
);
```

## 10. Example: automation_outbox and automation_inbox

Use for durable handoff across engines, modules, channels, or nodes.

```sql
CREATE TABLE automation_outbox (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  process_record_id BIGINT UNSIGNED NULL,
  message_key VARCHAR(120) NOT NULL,
  destination VARCHAR(120) NOT NULL,
  payload_json LONGTEXT NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'pending',
  published_at DATETIME NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_outbox_company (company_id),
  INDEX idx_outbox_status (status),
  INDEX idx_outbox_destination (destination)
);

CREATE TABLE automation_inbox (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  message_key VARCHAR(120) NOT NULL,
  source VARCHAR(120) NOT NULL,
  payload_json LONGTEXT NOT NULL,
  consumed_at DATETIME NULL,
  disposition VARCHAR(40) NOT NULL DEFAULT 'received',
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  UNIQUE KEY uniq_inbox_message (company_id, message_key),
  INDEX idx_inbox_source (source),
  INDEX idx_inbox_disposition (disposition)
);
```

## 11. Example: automation_runtime_locks

Use when overlap prevention and singleton work windows must survive process restarts.

```sql
CREATE TABLE automation_runtime_locks (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  company_id BIGINT UNSIGNED NOT NULL,
  lock_key VARCHAR(180) NOT NULL,
  holder_type VARCHAR(80) NULL,
  holder_id VARCHAR(120) NULL,
  acquired_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  released_at DATETIME NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  UNIQUE KEY uniq_runtime_lock (company_id, lock_key)
);
```

## 12. Minimal relationships

Suggested relationship spine:

- `process_records` 1→many `automation_runs`
- `automation_runs` 1→many `automation_retries`
- `process_records` 1→many `automation_approvals`
- `process_records` 1→many `automation_outbox`
- `process_records` 1→many `automation_dead_letters`

## 13. Tenant and audit rules

All runtime tables should:

- include `company_id`
- avoid cross-tenant joins without scope
- preserve timestamps for request, decision, start, finish, and failure
- keep stable keys for replay and dedupe
- never rely on queue payloads alone as the source of truth

## 14. What not to do

Avoid:

- storing runtime status directly on module entities only
- mixing approval state into unrelated business tables
- using queue attempt counters as the only retry record
- deleting dead-letter payloads before operator review
- building one-off runtime tables inside each module

## 15. Recommended implementation order

1. Add `process_records`
2. Add `automation_runs`
3. Add `automation_approvals`
4. Add `automation_retries`
5. Add `automation_dead_letters`
6. Add `automation_outbox` / `automation_inbox`
7. Add locks and trigger logs if needed

## 16. Result

These schemas give Titan automation a durable spine for process continuity, approval pause/resume, retry safety, dead-letter quarantine, relay handoff, and replay support while keeping module business tables clean and tenant-safe.
