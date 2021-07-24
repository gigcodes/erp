<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToProductPushInformationHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_push_information_histories', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_push_information_histories', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');

        });
    }
}
