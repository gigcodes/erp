<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductsAddIndexesForBrandSupplierIsOnSaleListingApprovedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('brand')->change();
            $table->index('brand');
            $table->index('supplier');
            $table->index('is_on_sale');
            $table->index('listing_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function ($table) {
            $table->dropIndex('products_brand_index');
            $table->dropIndex('products_supplier_index');
            $table->dropIndex('products_is_on_sale_index');
            $table->dropIndex('products_listing_approved_at_index');
        });
    }
}
