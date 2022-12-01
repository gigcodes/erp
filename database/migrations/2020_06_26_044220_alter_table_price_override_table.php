<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AlterTablePriceOverrideTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_overrides', function ($table) {
            $table->string('brand_segment')->nullable()->after('brand_id');
            $table->integer('store_website_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_overrides', function ($table) {
            $table->dropColumn('brand_segment');
            $table->dropColumn('store_website_id');
        });
    }
}
