# Module + Plugin Split Rules

Status: Canonical draft
Layer: Integration discipline

## Build Together, Split Strictly

Design the module and Filament plugin together so the UX fits the domain, but keep ownership separate.

## Put It In The Module If

- it must work from API
- it must work from PWA/mobile
- it must work from jobs/queues
- it must work from Titan chat/automation
- it must work from imports/exports
- it must work if Filament is removed

Examples:
- `CreateBookingAction`
- `AssignVisitAction`
- `InvoiceReminderJob`
- `BookingConfirmed` event
- `DispatchAvailabilityService`

## Put It In Filament If

- it exists only for admin/operator presentation
- it renders a panel workflow
- it configures forms/tables/filters/widgets
- it wraps module actions for panel usage

Examples:
- `BookingResource`
- `DispatchBoardPage`
- `LateArrivalWidget`
- `AssignBookingBulkAction`

## Never Duplicate

Bad:
- controller creates record one way
- API creates it another way
- Filament creates it a third way

Good:
- module action is source of truth
- controller calls action
- API calls action
- Filament calls action
- import job calls action

## Removal Test

A correct architecture passes both tests:

### Remove Filament
The module still works.

### Remove One UI Surface
Other UI surfaces still work.
