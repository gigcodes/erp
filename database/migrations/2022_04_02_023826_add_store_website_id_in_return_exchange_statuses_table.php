<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreWebsiteIdInReturnExchangeStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_exchange_statuses', function (Blueprint $table) {
            $table->integer('store_website_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_exchange_statuses', function (Blueprint $table) {
            $table->dropColumn('store_website_id');
        });
    }
}
