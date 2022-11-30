<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddProductIdToCustomerCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select('ALTER TABLE `customer_charities` ADD `product_id` INT(11) NULL AFTER `category_id`;');
        DB::select('ALTER TABLE `customer_charities` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_charities', function (Blueprint $table) {
            //
        });
    }
}
