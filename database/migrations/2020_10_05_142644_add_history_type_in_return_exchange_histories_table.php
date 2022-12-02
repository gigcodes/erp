<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHistoryTypeInReturnExchangeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_exchange_histories', function (Blueprint $table) {
            $table->string('history_type')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_exchange_histories', function (Blueprint $table) {
            //
        });
    }
}
