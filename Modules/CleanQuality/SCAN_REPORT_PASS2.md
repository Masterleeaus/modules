# QualityControl scan report — pass 2

## Fixed
- Installer metadata reinforced in `module.json` and package bridge config.
- Schedule deletion now always deletes the schedule and its linked calendar event.
- Inspection checklist item updates now preserve `schedule_id` for newly created items.
- File upload now iterates uploaded files safely and enforces permission checks.
- Floor/tower relations and migrations now tolerate missing optional Units tables.
- Recurring schedule module-setting creation now tolerates installs where `ModuleSetting` is unavailable.

## Drift / risk found
- Legacy ticket-oriented inspection views still exist and likely need a later cleanup pass.
- Placeholder DTOs and pass-stub tests remain; they are harmless but should be replaced or pruned in a later quality pass.
- Some legacy routes and UI strings still use mixed `inspection::` and `quality_control::` aliases by design for backward compatibility.
