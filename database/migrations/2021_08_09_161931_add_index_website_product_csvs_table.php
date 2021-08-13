<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexWebsiteProductCsvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("website_product_csvs",function(Blueprint $table) {
        //    $table->string('path')->nullable()->change();
            $table->dropColumn('path');

        });
        Schema::table("website_product_csvs",function(Blueprint $table) {
           $table->string('path')->nullable();

        });
       
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
