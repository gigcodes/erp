<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStoreWebsiteToCustomerCharityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_charity', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select('CREATE TABLE `sololuxury`.`customer_charity_website_stores` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `customer_charity_id` INT(11) NULL , `website_store_id` INT(11) NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;');
    }
}
