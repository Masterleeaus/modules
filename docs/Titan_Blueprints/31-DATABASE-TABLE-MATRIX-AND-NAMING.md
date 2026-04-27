# Database Table Matrix and Naming

## Purpose
Define how tables should be grouped and named across core, platform, and modules.

## Core conventions
- core platform tables may remain in main schema where appropriate
- module-owned tables should use explicit domain naming
- avoid ambiguous names like `settings`, `logs`, `items`

## Recommended table families

### Platform
- `platform_manifests`
- `signal_log`
- `aegis_log`
- `sentinel_log`
- `automation_runs`
- `workflow_runs`
- `workflow_steps`
- `outbox_messages`
- `dead_letters`

### AI
- `ai_context_packs`
- `ai_tool_calls`
- `ai_memory_entries`
- `ai_evaluations`
- `ai_proposals`
- `ai_decisions`

### Module example: bookings
- `bookings`
- `booking_assignments`
- `booking_status_logs`
- `booking_notes`
- `booking_reminders`

## Rules
- every tenant-owned table includes `company_id`
- status/history tables should be separate when audit value matters
- avoid one giant settings table for unrelated concerns
- use explicit log table names instead of generic `logs`
