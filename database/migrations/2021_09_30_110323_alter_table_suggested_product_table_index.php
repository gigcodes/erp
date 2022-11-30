<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableSuggestedProductTableIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \DB::statement('ALTER TABLE `suggested_products` ADD INDEX(`customer_id`);');
        \DB::statement('ALTER TABLE `suggested_product_lists` ADD INDEX(`product_id`);');
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
}
