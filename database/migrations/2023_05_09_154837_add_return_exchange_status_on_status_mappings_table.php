<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturnExchangeStatusOnStatusMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_mappings', function (Blueprint $table) {
            $table->integer('return_exchange_status_id')->after('shipping_status_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status_mappings', function (Blueprint $table) {
            $table->dropColumn('return_exchange_status_id');
        });
    }
}
