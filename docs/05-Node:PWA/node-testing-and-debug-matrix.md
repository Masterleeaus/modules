# Node Testing and Debug Matrix

## Purpose
Provides the minimum test matrix Agent 5 should use before claiming the PWA/node layer is reliable.

## Test classes
### Installability
- manifest valid
- service worker registers
- shell opens from homescreen/install surface

### Offline
- shell loads with network disabled
- offline page appears for uncached routes
- local actions queue correctly while offline

### Sync
- queued mutations replay after reconnect
- retries increment correctly
- failed items remain inspectable

### Push
- subscribe
- store endpoint
- receive notification
- unsubscribe

### Auth
- expired session path handled cleanly
- token rotation keeps queue intact
- user switch clears or rebinds node state appropriately

### Upgrade
- new worker installs
- stale cache invalidates
- update banner/refresh flow works

## Browser/device matrix
- Chrome desktop
- Android Chrome PWA
- iOS Safari web app mode
- tablet layout
- low-connectivity simulation

## Debug artifacts
Agent 5 should capture:
- failing route and response code
- worker registration state
- queue counts by status
- current manifest version
- auth/session state summary
- tenant/company context summary
