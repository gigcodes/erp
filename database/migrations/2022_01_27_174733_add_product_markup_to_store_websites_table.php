<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductMarkupToStoreWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->double('product_markup')->default(0)->after('remote_software');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->dropColumn('product_markup');
        });
    }
}
