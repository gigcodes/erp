<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingStatusToProductStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_status_histories', function (Blueprint $table) {
            $table->tinyInteger('pending_status')->nullable()->after('new_status')->default(0)->comment('0 No, 1 Yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_status_histories', function (Blueprint $table) {
            //
        });
    }
}
