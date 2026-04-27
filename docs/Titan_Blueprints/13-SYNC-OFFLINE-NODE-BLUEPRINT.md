# Sync + Offline + Node Blueprint

Status: Canonical draft  
Layer: Device runtime, local-first sync, offline continuity

## Role

This layer supports device-first operation. PWA and mobile-style surfaces must continue functioning offline, collect work locally, and sync through envelopes when connectivity returns.

## Tree

```text
app/Platform/Sync/
├─ Nodes/
├─ DeviceRegistry/
├─ EnvelopeSync/
├─ Outbound/
├─ Inbound/
├─ ConflictResolution/
├─ Snapshots/
├─ Checkpoints/
├─ Replay/
├─ OfflineCaches/
├─ Attachments/
├─ Security/
└─ Support/
```

## Core Concepts

### Nodes
A node may be:
- browser PWA instance
- tablet worker device
- owner command device
- office desktop session
- server node

### Envelope Sync
All sync traffic should use normalized envelopes with:
- node ID
- company ID
- object type
- object ID
- operation type
- local version
- remote version
- changed fields
- timestamp
- attachment refs

### Offline Cache
Store locally:
- current jobs/visits
- checklists
- allowed forms
- site memory
- required media queue
- pending messages
- pending approvals assigned to that device

### Conflict Resolution
Support strategies such as:
- server wins
- latest timestamp wins
- field-level merge
- manual review queue
- policy-based merge by entity type

## Checkpoints and Replay
- keep sync checkpoints
- rebuild device state
- replay unsent envelopes after reconnect
- recover partial uploads

## Security
- per-device trust record
- revoked device handling
- local encryption for sensitive caches where possible
- signed sync payloads for critical paths

## Surface Fit
This layer underpins:
- Titan Portal
- Titan Go
- Titan Command
- browser PWA mode
