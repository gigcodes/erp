<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMagentoValueToCharityBrand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select("UPDATE `store_website_brands` SET `magento_value` = '242' WHERE brand_id IN (SELECT id FROM `brands` WHERE `name` LIKE 'charity' ORDER BY `id` DESC)");
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
