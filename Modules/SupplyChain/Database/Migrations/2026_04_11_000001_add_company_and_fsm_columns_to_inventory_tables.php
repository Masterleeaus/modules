<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add company_id + FSM columns to inventory tables:
 * - warehouses      : company_id, address, type, is_active
 * - inventory_items : company_id, field_item_id (FK→FieldItems items)
 * - movements       : company_id, user_id, reference
 * - transfers       : company_id (status enum already exists)
 * - purchase_orders : company_id, ordered_by, expected_date, supplier_id FK
 * - stock_levels    : company_id, qty_reserved, qty_available
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── warehouses ───────────────────────────────────────────────
        if (Schema::hasTable('warehouses')) {
            Schema::table('warehouses', function (Blueprint $t) {
                if (!Schema::hasColumn('warehouses', 'company_id')) {
                    $t->unsignedBigInteger('company_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('warehouses', 'address')) {
                    $t->string('address', 500)->nullable()->after('code');
                }
                if (!Schema::hasColumn('warehouses', 'type')) {
                    $t->string('type', 50)->default('depot')->after('address');
                    // depot | van | office
                }
                if (!Schema::hasColumn('warehouses', 'is_active')) {
                    $t->boolean('is_active')->default(true)->after('type');
                }
            });
        }

        // ── inventory_items ──────────────────────────────────────────
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $t) {
                if (!Schema::hasColumn('inventory_items', 'company_id')) {
                    $t->unsignedBigInteger('company_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('inventory_items', 'field_item_id')) {
                    // Nullable FK to FieldItems items table (may or may not be active)
                    $t->unsignedBigInteger('field_item_id')->nullable()->after('company_id');
                }
            });

            // Add FK only when the items table exists (FieldItems module)
            if (Schema::hasTable('items') && !$this->foreignExists('inventory_items', 'inventory_items_field_item_id_foreign')) {
                Schema::table('inventory_items', function (Blueprint $t) {
                    $t->foreign('field_item_id', 'inventory_items_field_item_id_foreign')
                      ->references('id')->on('items')
                      ->nullOnDelete();
                });
            }
        }

        // ── movements ───────────────────────────────────────────────
        if (Schema::hasTable('movements')) {
            Schema::table('movements', function (Blueprint $t) {
                if (!Schema::hasColumn('movements', 'company_id')) {
                    $t->unsignedBigInteger('company_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('movements', 'user_id')) {
                    $t->unsignedBigInteger('user_id')->nullable()->after('company_id');
                }
                if (!Schema::hasColumn('movements', 'reference')) {
                    $t->string('reference', 191)->nullable()->after('note');
                }
            });
        }

        // ── transfers ───────────────────────────────────────────────
        if (Schema::hasTable('transfers')) {
            Schema::table('transfers', function (Blueprint $t) {
                if (!Schema::hasColumn('transfers', 'company_id')) {
                    $t->unsignedBigInteger('company_id')->nullable()->after('id');
                }
                // Widen status to include pending / in_transit / received
                if (Schema::hasColumn('transfers', 'status')) {
                    $t->string('status', 32)->default('pending')->change();
                }
            });
        }

        // ── purchase_orders ──────────────────────────────────────────
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $t) {
                if (!Schema::hasColumn('purchase_orders', 'company_id')) {
                    $t->unsignedBigInteger('company_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('purchase_orders', 'ordered_by')) {
                    $t->unsignedBigInteger('ordered_by')->nullable()->after('supplier_id');
                }
                if (!Schema::hasColumn('purchase_orders', 'expected_date')) {
                    $t->date('expected_date')->nullable()->after('ordered_at');
                }
            });
        }

        // ── stock_levels ─────────────────────────────────────────────
        if (Schema::hasTable('stock_levels')) {
            Schema::table('stock_levels', function (Blueprint $t) {
                if (!Schema::hasColumn('stock_levels', 'company_id')) {
                    $t->unsignedBigInteger('company_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('stock_levels', 'qty_reserved')) {
                    $t->decimal('qty_reserved', 14, 4)->default(0)->after('on_hand');
                }
                if (!Schema::hasColumn('stock_levels', 'qty_available')) {
                    // computed column placeholder (maintained in app logic)
                    $t->decimal('qty_available', 14, 4)->default(0)->after('qty_reserved');
                }
            });
        }
    }

    public function down(): void
    {
        // Remove added columns in reverse order

        if (Schema::hasTable('stock_levels')) {
            Schema::table('stock_levels', function (Blueprint $t) {
                $t->dropColumnIfExists(['company_id', 'qty_reserved', 'qty_available']);
            });
        }

        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $t) {
                $t->dropColumnIfExists(['company_id', 'ordered_by', 'expected_date']);
            });
        }

        if (Schema::hasTable('transfers')) {
            Schema::table('transfers', function (Blueprint $t) {
                $t->dropColumnIfExists('company_id');
            });
        }

        if (Schema::hasTable('movements')) {
            Schema::table('movements', function (Blueprint $t) {
                $t->dropColumnIfExists(['company_id', 'user_id', 'reference']);
            });
        }

        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $t) {
                if ($this->foreignExists('inventory_items', 'inventory_items_field_item_id_foreign')) {
                    $t->dropForeign('inventory_items_field_item_id_foreign');
                }
                $t->dropColumnIfExists(['company_id', 'field_item_id']);
            });
        }

        if (Schema::hasTable('warehouses')) {
            Schema::table('warehouses', function (Blueprint $t) {
                $t->dropColumnIfExists(['company_id', 'address', 'type', 'is_active']);
            });
        }
    }

    /** Check whether a named foreign key already exists (avoids duplicates). */
    private function foreignExists(string $table, string $keyName): bool
    {
        try {
            $fks = \Illuminate\Support\Facades\Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys($table);
            foreach ($fks as $fk) {
                if ($fk->getName() === $keyName) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // If Doctrine is not available or the table doesn't exist, fall back to false
        }
        return false;
    }
};
