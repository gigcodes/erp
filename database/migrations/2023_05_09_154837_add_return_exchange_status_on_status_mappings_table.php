<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
