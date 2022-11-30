<?php

use Illuminate\Database\Migrations\Migration;

class AlterTblProductsAddIndexToSupplierid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `products` ADD INDEX(`supplier_id`)');
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
