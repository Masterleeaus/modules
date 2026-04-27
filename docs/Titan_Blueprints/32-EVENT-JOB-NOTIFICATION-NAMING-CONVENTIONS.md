# Event, Job, and Notification Naming Conventions

## Events
Use past-tense domain facts.
- `BookingCreated`
- `BookingAssigned`
- `InvoiceSent`
- `SignalApproved`

## Jobs
Use imperative queued work.
- `SendBookingReminderJob`
- `GenerateRunSheetJob`
- `DispatchApprovedSignalJob`
- `SyncDeviceEnvelopeJob`

## Notifications
Use recipient-visible outcomes.
- `BookingReminderNotification`
- `InvoiceOverdueNotification`
- `WorkerAssignedNotification`

## Mailables
Use explicit outbound document/communication names.
- `BookingConfirmationMail`
- `InvoiceReminderMail`

## Listeners
Describe reaction to an event.
- `QueueBookingReminderListener`
- `EmitSignalForCompletedVisitListener`
- `UpdateCapacityMetricsListener`

## Rules
- Events describe facts
- Jobs describe queued work
- Notifications describe user-visible delivery
- Listeners describe reactions
- one name = one purpose
