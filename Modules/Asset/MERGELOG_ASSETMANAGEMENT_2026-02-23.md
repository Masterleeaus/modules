# Merge log — AssetManagement → Asset (2026-02-23)

Moved/merged files:

- `source/AssetManagement/Entities/AssetMaintenance.php` → `target/Asset/Entities/AssetMaintenance.php`
- `source/AssetManagement/Entities/AssetTransaction.php` → `target/Asset/Entities/AssetTransaction.php`
- `source/AssetManagement/Entities/AssetWarranty.php` → `target/Asset/Entities/AssetWarranty.php`
- `source/AssetManagement/Utils/AssetUtil.php` → `target/Asset/Utils/AssetUtil.php`
- `source/AssetManagement/Notifications/AssetAssignedForMaintenance.php` → `target/Asset/Notifications/AssetAssignedForMaintenance.php`
- `source/AssetManagement/Notifications/AssetSentForMaintenance.php` → `target/Asset/Notifications/AssetSentForMaintenance.php`
- `source/AssetManagement/Http/Controllers/AssetAllocationController.php` → `target/Asset/Http/Controllers/AssetAllocationController.php`
- `source/AssetManagement/Http/Controllers/AssetMaitenanceController.php` → `target/Asset/Http/Controllers/AssetMaintenanceController.php`
- `source/AssetManagement/Http/Controllers/RevokeAllocatedAssetController.php` → `target/Asset/Http/Controllers/RevokeAllocatedAssetController.php`
- `source/AssetManagement/Http/Controllers/AssetSettingsController.php` → `target/Asset/Http/Controllers/AssetSettingsController.php`
- `source/AssetManagement/Http/Controllers/DataController.php` → `target/Asset/Http/Controllers/DataController.php`
- `source/AssetManagement/Resources/views/asset_maintenance/index.blade.php` → `target/Asset/Resources/views/asset_maintenance/index.blade.php`
- `source/AssetManagement/Resources/views/asset_maintenance/edit.blade.php` → `target/Asset/Resources/views/asset_maintenance/edit.blade.php`
- `source/AssetManagement/Resources/views/asset_maintenance/create.blade.php` → `target/Asset/Resources/views/asset_maintenance/create.blade.php`
- `source/AssetManagement/Resources/views/asset_allocation/index.blade.php` → `target/Asset/Resources/views/asset_allocation/index.blade.php`
- `source/AssetManagement/Resources/views/asset_allocation/edit.blade.php` → `target/Asset/Resources/views/asset_allocation/edit.blade.php`
- `source/AssetManagement/Resources/views/asset_allocation/create.blade.php` → `target/Asset/Resources/views/asset_allocation/create.blade.php`
- `source/AssetManagement/Resources/views/asset_revocation/index.blade.php` → `target/Asset/Resources/views/asset_revocation/index.blade.php`
- `source/AssetManagement/Resources/views/asset_revocation/create.blade.php` → `target/Asset/Resources/views/asset_revocation/create.blade.php`
- `source/AssetManagement/Resources/views/settings/index.blade.php` → `target/Asset/Resources/views/settings/index.blade.php`
- `source/AssetManagement/Resources/views/settings/notification_settings.blade.php` → `target/Asset/Resources/views/settings/notification_settings.blade.php`
- `source/AssetManagement/Resources/views/settings/prefix_settings.blade.php` → `target/Asset/Resources/views/settings/prefix_settings.blade.php`
- `source/AssetManagement/Resources/views/asset/index.blade.php` → `target/Asset/Resources/views/asset/index.blade.php`
- `source/AssetManagement/Resources/views/asset/edit.blade.php` → `target/Asset/Resources/views/asset/edit.blade.php`
- `source/AssetManagement/Resources/views/asset/dashboard.blade.php` → `target/Asset/Resources/views/asset/dashboard.blade.php`
- `source/AssetManagement/Resources/views/asset/create.blade.php` → `target/Asset/Resources/views/asset/create.blade.php`
- `source/AssetManagement/Resources/lang/.gitkeep` → `target/Asset/Resources/lang/assetmanagement/.gitkeep`
- `source/AssetManagement/Resources/lang/vi/lang.php` → `target/Asset/Resources/lang/assetmanagement/vi/lang.php`
- `source/AssetManagement/Resources/lang/sq/lang.php` → `target/Asset/Resources/lang/assetmanagement/sq/lang.php`
- `source/AssetManagement/Resources/lang/ro/lang.php` → `target/Asset/Resources/lang/assetmanagement/ro/lang.php`
- `source/AssetManagement/Resources/lang/pt/lang.php` → `target/Asset/Resources/lang/assetmanagement/pt/lang.php`
- `source/AssetManagement/Resources/lang/ps/lang.php` → `target/Asset/Resources/lang/assetmanagement/ps/lang.php`
- `source/AssetManagement/Resources/lang/ar/lang.php` → `target/Asset/Resources/lang/assetmanagement/ar/lang.php`
- `source/AssetManagement/Resources/lang/nl/lang.php` → `target/Asset/Resources/lang/assetmanagement/nl/lang.php`
- `source/AssetManagement/Resources/lang/hi/lang.php` → `target/Asset/Resources/lang/assetmanagement/hi/lang.php`
- `source/AssetManagement/Resources/lang/de/lang.php` → `target/Asset/Resources/lang/assetmanagement/de/lang.php`
- `source/AssetManagement/Resources/lang/id/lang.php` → `target/Asset/Resources/lang/assetmanagement/id/lang.php`
- `source/AssetManagement/Resources/lang/fr/lang.php` → `target/Asset/Resources/lang/assetmanagement/fr/lang.php`
- `source/AssetManagement/Resources/lang/es/lang.php` → `target/Asset/Resources/lang/assetmanagement/es/lang.php`
- `source/AssetManagement/Resources/lang/en/lang.php` → `target/Asset/Resources/lang/assetmanagement/en/lang.php`
- `source/AssetManagement/Resources/lang/lo/lang.php` → `target/Asset/Resources/lang/assetmanagement/lo/lang.php`
- `source/AssetManagement/Resources/lang/ce/lang.php` → `target/Asset/Resources/lang/assetmanagement/ce/lang.php`
- `source/AssetManagement/Resources/lang/tr/lang.php` → `target/Asset/Resources/lang/assetmanagement/tr/lang.php`
- `source/AssetManagement/Database/Migrations/2020_08_20_173031_create_asset_transactions_table.php` → `target/Asset/Database/Migrations/2026_02_23_1000_2020_08_20_173031_create_asset_transactions_table.php`
- `source/AssetManagement/Database/Migrations/2021_10_29_110841_create_asset_warranties_table.php` → `target/Asset/Database/Migrations/2026_02_23_1000_2021_10_29_110841_create_asset_warranties_table.php`
- `source/AssetManagement/Database/Migrations/2022_03_26_062215_create_asset_maintenances_table.php` → `target/Asset/Database/Migrations/2026_02_23_1000_2022_03_26_062215_create_asset_maintenances_table.php`
- `source/AssetManagement/Database/Migrations/2022_05_11_070711_add_maintenance_note_column_to_asset_maintenances_table.php` → `target/Asset/Database/Migrations/2026_02_23_1000_2022_05_11_070711_add_maintenance_note_column_to_asset_maintenances_table.php`

Source pruned: moved files were deleted from `source/AssetManagement` after merge.


## Pass 2 (2026-02-23)
- Added scan action routes (issue/return/report + maintenance + allocation)
- Implemented scan action controller methods (best-effort, tenant-safe)
- Updated scan blade with big action buttons
- Patched migrations for idempotent qrcode_id where applicable


## Hotfix + Hardening (2026-02-23)
- Fixed migration class name collision by converting create_asset_transactions migration to anonymous class + hasTable guard.
- Hardened 2021_08_11_123557_add_owned_by_columns_assets migration to guard additional columns.

- Fixed CreateAssetWarrantiesTable migration (anonymous + hasTable guard).

- Fixed CreateAssetMaintenancesTable migration (anonymous + hasTable guard).


## Full Module Scan Hardening (2026-02-23)
- Scanned all Module/Asset migrations for named-class collisions.
- Converted remaining named migration to anonymous (maintenance_note add-column).
- Added hasColumn guards for the added maintenance note column.
