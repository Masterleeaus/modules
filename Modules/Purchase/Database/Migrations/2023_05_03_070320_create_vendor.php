<?php

use App\Models\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Purchase\Entities\PurchaseNotificationSetting;
use Modules\Purchase\Entities\PurchaseManagementSetting;
use Modules\Purchase\Entities\PurchaseSetting;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        \App\Models\Module::validateVersion(PurchaseManagementSetting::MODULE_NAME);

        // Purchase Settings Table
        if (!Schema::hasTable('purchase_settings')) {
            Schema::create('purchase_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('company_id')->nullable();
                $table->string('purchase_order_prefix', 10)->default('PO');
                $table->string('purchase_order_number_separator', 10)->default('#');
                $table->integer('purchase_order_number_digit')->default('3');
                $table->string('bill_prefix', 10)->default('PO');
                $table->string('bill_number_separator', 10)->default('#');
                $table->integer('bill_number_digit')->default('3');
                $table->string('vendor_credit_prefix', 10)->default('VC');
                $table->string('vendor_credit_number_seprator', 10)->default('#');
                $table->integer('vendor_credit_number_digit')->default('3');
                $table->string('purchase_code')->nullable()->default(null);
                $table->timestamps();
            });
        }


        // Product Table changes
        if (Schema::hasTable('products')) {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'type')) {
                $table->enum('type', ['goods', 'service'])->default('goods')->after('default_image')->nullable();
            }
            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->string('purchase_price')->after('price')->nullable();
            }
            if (!Schema::hasColumn('products', 'purchase_information')) {
                $table->enum('purchase_information', [1, 0])->default(0)->after('purchase_price');
            }
            if (!Schema::hasColumn('products', 'track_inventory')) {
                $table->enum('track_inventory', [1, 0])->default(0)->after('purchase_information');
            }
            if (!Schema::hasColumn('products', 'sales_description')) {
                $table->longText('sales_description')->after('track_inventory')->nullable();
            }
            if (!Schema::hasColumn('products', 'purchase_description')) {
                $table->longText('purchase_description')->after('sales_description')->nullable();
            }
            if (!Schema::hasColumn('products', 'opening_stock')) {
                $table->integer('opening_stock')->after('purchase_description')->nullable();
            }
            if (!Schema::hasColumn('products', 'rate_per_unit')) {
                $table->double('rate_per_unit')->after('opening_stock')->nullable();
            }
            if (!Schema::hasColumn('products', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('type');
            }
        });
    }

        Schema::whenTableDoesntHaveColumn('products', 'sku', function (Blueprint $table) {
            $table->string('sku', 100)->after('hsn_sac_code')->nullable();
        });


        // Purchase Vendor Table
        if (!Schema::hasTable('purchase_vendors')) {
            Schema::create('purchase_vendors', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('company_id')->nullable();
                $table->string('primary_name', 50);
                $table->string('company_name', 50)->nullable();
                $table->string('email', 50)->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('website', 50)->nullable();
                $table->unsignedInteger('currency_id')->unsigned()->nullable();
                $table->foreign(['currency_id'])->references(['id'])->on('currencies')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->double('opening_balance')->nullable();
                $table->string('billing_address', 256)->nullable();
                $table->string('shipping_address', 256)->nullable();
                $table->integer('added_by')->unsigned()->nullable();
                $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
                $table->integer('last_updated_by')->unsigned()->nullable();
                $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
                $table->timestamps();
            });
        }
        // Purchase Vendor Contacts Table
        if (!Schema::hasTable('purchase_vendor_contacts')) {
            Schema::create('purchase_vendor_contacts', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('company_id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('purchase_vendor_id')->index('purchase_vendor_contacts_purchase_vendor_id_foreign');
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->string('title')->nullable();
                $table->string('contact_name', 50);
                $table->string('email', 50)->nullable();
                $table->string('phone', 30)->nullable();
                $table->timestamps();
            });
        }
        // Purchase Vendor Notes Table
        if (!Schema::hasTable('purchase_vendor_notes')) {
            Schema::create('purchase_vendor_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('company_id')->nullable();
                $table->integer('purchase_vendor_id')->unsigned();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->string('note_title', 100);
                $table->boolean('note_type')->default(false);
                $table->text('note_details')->nullable();
                $table->boolean('ask_password')->default(false);
                $table->integer('member_id')->unsigned()->nullable();
                $table->foreign(['member_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }
        // Purchase Order Setting Table
        if (!Schema::hasTable('purchase_order_settings')) {
            Schema::create('purchase_order_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('purchase_prefix', 10)->nullable()->default('PO');
                $table->string('purchase_number_separator', 10)->nullable()->default('#');
                $table->integer('purchase_number_digit')->nullable()->default(3);
                $table->timestamps();
            });
        }
        // Purchase orders Table
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('purchase_order_number', 50)->nullable();
                $table->integer('vendor_id')->unsigned()->nullable();
                $table->foreign('vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('bank_account_id')->unsigned()->nullable();
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade')->onUpdate('cascade');
                $table->bigInteger('address_id')->unsigned()->nullable();
                $table->foreign('address_id')->references('id')->on('company_addresses')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('currency_id')->unsigned()->nullable();
                $table->foreign(['currency_id'])->references(['id'])->on('currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->integer('default_currency_id')->unsigned()->nullable();
                $table->foreign(['default_currency_id'])->references(['id'])->on('currencies')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->double('exchange_rate', 16, 2)->nullable();
                $table->date('purchase_date')->nullable();
                $table->text('note')->nullable();
                $table->date('expected_delivery_date')->nullable();
                $table->double('discount', 16, 2)->default(0);
                $table->double('sub_total', 16, 2)->default(0);
                $table->double('total', 16, 2)->default(0);
                $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
                $table->boolean('send_status')->nullable()->default(0);
                $table->enum('purchase_status', ['draft', 'open', 'issued', 'accepted', 'rejected', 'canceled', 'closed'])->default('Open');
                $table->enum('billed_status', ['billed', 'unbilled'])->default('unbilled');
                $table->enum('delivery_status', ['delivered', 'delivery_failed', 'in_transaction', 'not_started'])->default('not_started');
                $table->enum('calculate_tax', ['after_discount', 'before_discount'])->default('after_discount');
                $table->unsignedInteger('added_by')->nullable()->index('purchase_orders_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('purchase_orders_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->timestamps();
            });
        }
        // Purchase Items Table
        if (!Schema::hasTable('purchase_items')) {
            Schema::create('purchase_items', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_order_id')->unsigned()->nullable();
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade')->onUpdate('cascade');
                $table->bigInteger('unit_id')->unsigned()->nullable();
                $table->foreign('unit_id')->references('id')->on('unit_types')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
                $table->string('item_name', 256)->nullable();
                $table->text('item_summary')->nullable();
                $table->enum('type', ['item', 'discount', 'tax'])->default('item');
                $table->double('quantity', 16, 2)->default(0);
                $table->double('unit_price', 16, 2)->default(0);
                $table->double('amount', 16, 2)->default(0);
                $table->string('hsn_sac_code', 30)->nullable();
                $table->timestamps();
            });
        }
        // Purchase Item Taxes Table
        if (!Schema::hasTable('purchase_item_taxes')) {
            Schema::create('purchase_item_taxes', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->integer('purchase_order_id')->unsigned()->nullable();
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_item_id')->unsigned()->nullable();
                $table->foreign('purchase_item_id')->references('id')->on('purchase_items')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('tax_id')->unsigned()->nullable();
                $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('cascade')->onUpdate('cascade');
                $table->timestamps();
            });
        }
        // Purchase Bills Table
        if (!Schema::hasTable('purchase_bills')) {
            Schema::create('purchase_bills', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_bill_number');
                $table->integer('purchase_vendor_id')->unsigned();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->date('bill_date')->nullable();
                $table->integer('purchase_order_id')->unsigned()->nullable();
                $table->double('discount', 16, 2)->default(0);
                $table->double('sub_total', 16, 2)->default(0);
                $table->double('total', 16, 2)->default(0);
                $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade')->onUpdate('cascade');
                $table->enum('status', ['draft', 'open', 'paid', 'partially_paid'])->default('draft');
                $table->text('note')->nullable();
                $table->boolean('credit_note')->default(false);
                $table->double('due_amount', 8, 2)->default(0);
                $table->unsignedInteger('added_by')->nullable();
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->timestamps();
            });
        }
        // Purchase Stock Adjustment Reason Table
        if (!Schema::hasTable('purchase_stock_adjustment_reasons')) {
            Schema::create('purchase_stock_adjustment_reasons', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('name', 256);
                $table->timestamps();
            });
        }
        // Purchase Inventory Adjustment Table
        if (!Schema::hasTable('purchase_inventory_adjustment')) {
            Schema::create('purchase_inventory_adjustment', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('reason_id')->unsigned()->nullable();
                $table->foreign('reason_id')->references(['id'])->on('purchase_stock_adjustment_reasons')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->enum('type', ['quantity', 'value'])->default('quantity');
                $table->date('date')->nullable();
                $table->integer('default_image')->nullable();
                $table->timestamps();
            });
        }
        // Purchase Inventory Files Table
        if (!Schema::hasTable('purchase_inventory_files')) {
            Schema::create('purchase_inventory_files', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('inventory_id')->unsigned()->nullable();
                $table->foreign('inventory_id')->references(['id'])->on('purchase_inventory_adjustment')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->text('description')->nullable();
                $table->string('google_url')->nullable();
                $table->string('dropbox_link')->nullable();
                $table->string('external_link_name')->nullable();
                $table->text('external_link')->nullable();
                $table->unsignedInteger('added_by')->nullable()->index('project_files_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('project_files_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->timestamps();
            });
        }
        // Purchase Stock Adjustment Table
        if (!Schema::hasTable('purchase_stock_adjustments')) {
            Schema::create('purchase_stock_adjustments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('inventory_id')->unsigned()->nullable();
                $table->foreign('inventory_id')->references(['id'])->on('purchase_inventory_adjustment')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id')->references(['id'])->on('products')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->integer('reason_id')->unsigned()->nullable();
                $table->foreign('reason_id')->references(['id'])->on('purchase_stock_adjustment_reasons')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->enum('type', ['quantity', 'value'])->default('quantity');
                $table->date('date')->nullable();
                $table->string('reference_number', 50)->nullable();
                $table->double('net_quantity', 16, 2)->nullable();
                $table->integer('quantity_adjustment')->default(0)->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['draft', 'converted'])->default('draft');
                $table->double('changed_value', 16, 2)->default(0)->nullable();
                $table->double('adjusted_value', 16, 2)->default(0)->nullable();
                $table->timestamps();
            });
        }
        // Purchase Vendor Payments Table
        if (!Schema::hasTable('purchase_vendor_payments')) {
            Schema::create('purchase_vendor_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_id')->unsigned();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->date('payment_date')->nullable();
                $table->unsignedInteger('vendor_credit_id')->nullable()->index('purchase_vendor_credits_id_foreign');
                $table->integer('bank_account_id')->unsigned()->nullable();
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade')->onUpdate('cascade');
                $table->double('received_payment');
                $table->enum('status', ['complete', 'pending', 'failed'])->default('complete');
                $table->double('excess_payment');
                $table->dateTime('paid_on')->nullable()->index();
                $table->boolean('notify_vendor')->nullable()->default(0);
                $table->text('internal_note')->default(null)->nullable();
                $table->integer('added_by')->unsigned()->nullable();
                $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
                $table->integer('last_updated_by')->unsigned()->nullable();
                $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_item_images')) {
            Schema::create('purchase_item_images', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedInteger('purchase_item_id')->index('purchase_item_images_purchase_item_id_foreign');
                $table->foreign(['purchase_item_id'])->references(['id'])->on('purchase_items')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('external_link')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_order_files')) {
            Schema::create('purchase_order_files', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedInteger('purchase_order_id')->index('purchase_files_invoice_id_foreign');
                $table->foreign(['purchase_order_id'])->references(['id'])->on('purchase_orders')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->unsignedInteger('added_by')->nullable()->index('purchase_files_added_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->unsignedInteger('last_updated_by')->nullable()->index('purchase_files_last_updated_by_foreign');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->string('filename', 200)->nullable();
                $table->string('hashname', 200)->nullable();
                $table->string('size', 200)->nullable();
                $table->timestamps();
            });
        }
        // Purchase Vendor History Table
        if (!Schema::hasTable('purchase_vendor_histories')) {
            Schema::create('purchase_vendor_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_notes_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_notes_id')->references('id')->on('purchase_vendor_notes')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_contact_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_contact_id')->references('id')->on('purchase_vendor_contacts')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->text('details')->nullable();
                $table->text('label')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_vendor_user_notes')) {
            Schema::create('purchase_vendor_user_notes', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('user_id')->index('purchase_vendor_user_notes_user_id_foreign');
                $table->unsignedInteger('vendor_note_id')->index('purchase_vendor_user_notes_vendor_note_id_foreign');
                $table->foreign(['vendor_note_id'])->references(['id'])->on('purchase_vendor_notes')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_product_histories')) {
            Schema::create('purchase_product_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_product_id')->unsigned()->nullable();
                $table->foreign('purchase_product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->text('label')->nullable();
                $table->text('details')->nullable();
                $table->text('type')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_vendor_credits')) {
            Schema::create('purchase_vendor_credits', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('vendor_id')->unsigned()->nullable();
                $table->foreign('vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->string('credit_note_no')->nullable();
                $table->date('credit_date')->nullable();
                $table->integer('currency_id')->unsigned();
                $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade')->onUpdate('cascade');
                $table->double('sub_total', 16, 2);
                $table->double('discount')->default(0);
                $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
                $table->double('total', 16, 2);
                $table->enum('status', ['open', 'closed'])->default('open');
                $table->text('hash')->nullable();
                $table->enum('calculate_tax', ['after_discount', 'before_discount'])->default('after_discount');
                $table->text('note')->nullable();
                $table->boolean('send_status')->default(true);
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('bill_id')->unsigned()->nullable();
                $table->foreign('bill_id')->references('id')->on('purchase_bills')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('payment_id')->unsigned()->nullable();
                $table->foreign('payment_id')->references('id')->on('purchase_vendor_payments')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedInteger('added_by')->nullable()->index('purchase_vendor_credits_added_by_foreign');
                $table->unsignedInteger('last_updated_by')->nullable()->index('purchase_vendor_credits_last_updated_by_foreign');
                $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->foreign(['last_updated_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_payment_bills')) {
            Schema::create('purchase_payment_bills', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->integer('purchase_vendor_payment_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_payment_id')->references('id')->on('purchase_vendor_payments')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_bill_id')->unsigned()->nullable();
                $table->foreign('purchase_bill_id')->references('id')->on('purchase_bills')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_credits_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_credits_id')->references('id')->on('purchase_vendor_credits')->onDelete('cascade')->onUpdate('cascade');
                $table->string('gateway')->nullable();
                $table->double('total_paid', 16, 2)->default(0)->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_vendor_items')) {
            Schema::create('purchase_vendor_items', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->integer('credit_id')->unsigned()->nullable();
                $table->foreign('credit_id')->references('id')->on('purchase_vendor_credits')->onDelete('cascade')->onUpdate('cascade');
                $table->string('item_name');
                $table->text('item_summary')->nullable();
                $table->enum('type', ['item', 'discount', 'tax'])->default('item');
                $table->double('quantity', 16, 2);
                $table->double('unit_price', 16, 2);
                $table->double('amount', 16, 2);
                $table->string('taxes')->nullable();
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
                $table->unsignedBigInteger('unit_id')->unsigned()->nullable()->default(null);
                $table->foreign('unit_id')->references('id')->on('unit_types')->onDelete('cascade')->onUpdate('cascade');
                $table->string('hsn_sac_code')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_vendor_credit_item_images')) {
            Schema::create('purchase_vendor_credit_item_images', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedInteger('vendor_item_id')->index('purchase_vendor_item_images_vendor_item_id_foreign');
                $table->foreign(['vendor_item_id'])->references(['id'])->on('purchase_vendor_items')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->string('filename');
                $table->string('hashname')->nullable();
                $table->string('size')->nullable();
                $table->string('external_link')->nullable();
                $table->timestamps();
            });

            Schema::create('purchase_payment_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_payment_id')->unsigned()->nullable();
                $table->foreign('purchase_payment_id')->references('id')->on('purchase_vendor_payments')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_order_id')->unsigned()->nullable();
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade')->onUpdate('cascade');
                $table->string('purchase_order')->nullable();
                $table->integer('purchase_bill_id')->unsigned()->nullable();
                $table->foreign('purchase_bill_id')->references('id')->on('purchase_bills')->onDelete('cascade')->onUpdate('cascade');
                $table->double('amount', 16, 2)->default(0)->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->text('details')->nullable();
                $table->text('label')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_vendor_credit_histories')) {
            Schema::create('purchase_vendor_credit_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_credit_id')->unsigned()->nullable();
                $table->foreign('purchase_credit_id')->references('id')->on('purchase_vendor_credits')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('amount')->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->text('label')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        // Purchase Bill History Table
        if (!Schema::hasTable('purchase_inventory_histories')) {
            Schema::create('purchase_inventory_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('inventory_id')->unsigned()->nullable();
                $table->foreign('inventory_id')->references(['id'])->on('purchase_inventory_adjustment')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->string('product_name')->nullable();
                $table->double('net_quantity', 16, 2)->nullable();
                $table->integer('quantity_adjustment')->default(0)->nullable();
                $table->double('changed_value', 16, 2)->default(0)->nullable();
                $table->double('adjusted_value', 16, 2)->default(0)->nullable();
                $table->integer('purchase_inventory_files_id')->unsigned()->nullable();
                $table->foreign('purchase_inventory_files_id')->references('id')->on('purchase_inventory_files')->onDelete('cascade')->onUpdate('cascade');
                $table->text('label')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_bill_histories')) {
            Schema::create('purchase_bill_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_bill_id')->unsigned()->nullable();
                $table->foreign('purchase_bill_id')->references('id')->on('purchase_bills')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_order_id')->unsigned()->nullable();
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade')->onUpdate('cascade');
                $table->string('purchase_order')->nullable();
                $table->integer('amount')->nullable();
                $table->date('bill_date')->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->text('label')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('purchase_order_histories')) {
            Schema::create('purchase_order_histories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_vendor_id')->unsigned()->nullable();
                $table->foreign('purchase_vendor_id')->references('id')->on('purchase_vendors')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('purchase_order_id')->unsigned()->nullable();
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->text('label')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('bank_transactions')) {
        Schema::table('bank_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_transactions', 'purchase_payment_id')) {
                $table->integer('purchase_payment_id')->unsigned()->nullable();
            }
            $table->foreign('purchase_payment_id')->references('id')->on('purchase_vendor_payments')->onDelete('set null')->onUpdate('cascade');
        });
    }

        if (!Schema::hasTable('purchase_notification_settings')) {
            Schema::create('purchase_notification_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->string('slug')->nullable();
                $table->string('setting_name');
                $table->enum('send_email', ['yes', 'no'])->default('no');
                $table->timestamps();
            });
        }

        $notificationSettings = [
            [
                'send_email' => 'yes',
                'setting_name' => 'New Purchase Order',
                'slug' => 'new-purchase-order',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'New Purchase Bill',
                'slug' => 'new-purchase-bill',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'Admin New Vendor Payment',
                'slug' => 'admin-new-vendor-payment',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'Update New Vendor Payment',
                'slug' => 'update-new-vendor-payment',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'Vendor Credit',
                'slug' => 'vendor-credit',
            ],
            [
                'send_email' => 'yes',
                'setting_name' => 'New Purchase Inventory',
                'slug' => 'new-purchase-inventory',
            ],
        ];

        $companies = Company::all();

        foreach ($companies as $company) {

            foreach ($notificationSettings as $notificationSetting) {
                $notificationSetting['company_id'] = $company->id;
                $notificationSetting = PurchaseNotificationSetting::firstOrNew($notificationSetting);
                $notificationSetting->saveQuietly();
            }

            $purchaseSetting = new PurchaseSetting();
            $purchaseSetting->company_id = $company->id;
            $purchaseSetting->save();
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        //
    }

};
