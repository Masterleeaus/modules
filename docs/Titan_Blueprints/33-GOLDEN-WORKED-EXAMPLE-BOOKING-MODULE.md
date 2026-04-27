# Golden Worked Example: Booking Module

## Purpose
Provide one canonical domain example that ties together module, Filament, API, jobs, events, notifications, and manifests.

## Flow
1. `StoreBookingRequest` validates input.
2. `CreateBookingAction` persists the booking.
3. `BookingCreated` event fires.
4. `QueueBookingReminderListener` dispatches `SendBookingReminderJob`.
5. `BookingReminderNotification` and optional `BookingConfirmationMail` are sent through communications layer.
6. `signals_manifest.json` allows the module to emit booking lifecycle signals.
7. Filament resource calls the same action/service, not its own duplicate path.

## Example folder map
```text
Modules/BookingManagement/
  Http/Requests/StoreBookingRequest.php
  Actions/CreateBookingAction.php
  Events/BookingCreated.php
  Listeners/QueueBookingReminderListener.php
  Jobs/SendBookingReminderJob.php
  Notifications/BookingReminderNotification.php
  Mail/BookingConfirmationMail.php
  Filament/Resources/BookingResource.php
  manifests/signals_manifest.json
  manifests/ai_tools.json
```

## Why this matters
This example proves the system is an engine:
- input validation
- mutation action
- event emission
- queued side effects
- user delivery
- AI/signal participation
- admin panel reuse
