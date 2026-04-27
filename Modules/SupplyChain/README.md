# SupplyChain (Worksuite SaaS Module) — v1.0
Merged Suppliers + Inventory into a single procurement-to-stock engine.
- Sidebar is plan-gated via the `supplychain` module flag and permissions.
- Tables include suppliers, stock_levels, purchase_orders (+items), goods_receipts (+items), and transfers.
- GRN receiving updates StockLevel + Movement IN; Transfer approves as OUT+IN pair.

## Install / Upgrade
1. Upload to `Modules/SupplyChain` or install via Super Admin → Modules → Install.
2. Run:
   php artisan optimize:clear
   php artisan migrate --force
   php artisan db:seed --class="Modules\SupplyChain\Database\Seeders\InventorySeeder"
3. Grant permissions in Roles & Permissions:
   - supplychain.view / supplychain.manage
   - supplychain.suppliers.*
   - supplychain.purchasing.*
   - supplychain.transfer.*

## Notes
- Costing: the current build records costs on GRN lines; valuation reports are a future step.
- Reorder: set min/max on stock_levels; later we’ll add a “Replenish” suggestion screen.
