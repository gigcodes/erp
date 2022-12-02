<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAddedFromCsvToProductPushInformationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_push_information_histories', function (Blueprint $table) {
            $table->boolean('is_added_from_csv')->default(1)->index();
            $table->boolean('old_is_added_from_csv')->default(1)->index();
            $table->boolean('old_is_available')->default(1)->index();
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
            $table->dropIndex('old_is_added_from_csv');
            $table->dropIndex('is_added_from_csv');
            $table->dropIndex('old_is_available');

            $table->dropColumn('old_is_added_from_csv');
            $table->dropColumn('is_added_from_csv');
            $table->dropColumn('old_is_available');
        });
    }
}
