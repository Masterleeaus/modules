# Asset Module Full Scan Report (2026-02-23)

## Migrations
- Total migrations: 20
- Named migrations remaining: 0
- Action: converted all module migrations to anonymous to prevent PHP class collisions in modular installs.

## Known collision fixes applied
- asset_transactions (anonymous + hasTable guard)
- asset_warranties (anonymous + hasTable guard)
- asset_maintenances (anonymous + hasTable guard)
- add_maintenance_note_column_to_asset_maintenances (anonymous + hasColumn guard)

## Notes
- If you still see a class-collision error, it will be coming from a different module or /database/migrations.
