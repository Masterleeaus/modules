# Node Role and Shell Map

## Purpose
Defines how different PWA shells map to node roles so Agent 5 can keep the runtime focused by user/job context instead of producing one overloaded shell.

## Canonical shells
### Titan Omni
Primary conversational and comms shell. Owns messaging, inbox, channel surfaces, and approval prompts.

### Titan Portal
General staff/admin mobile surface. Owns approvals, status review, lightweight records, and role-aware navigation.

### Titan Command
Owner/manager command surface. Owns oversight, dispatch visibility, exception handling, and live operational state.

### Titan Go
Field worker shell. Owns today view, jobs, checklists, proof capture, notes, and minimal communications.

### Titan Money
Finance/payment shell. Owns invoices, payment recovery, confirmations, and sensitive finance actions with stricter gates.

## Mapping rules
- same business graph, different shell focus
- same signal model, different UI priority
- same tenant boundary, different capability surface
- same memory spine, role-filtered presentation

## Agent 5 responsibilities
- detect role-specific capabilities needed by the shell
- prevent admin-density UI from leaking into worker shells
- keep offline queues/scopes isolated where required
- ensure each shell has an explicit execution boundary
