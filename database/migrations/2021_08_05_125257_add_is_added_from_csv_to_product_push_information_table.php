<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAddedFromCsvToProductPushInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_push_informations', function (Blueprint $table) {
            $table->boolean('is_added_from_csv')->default(1)->index();
            $table->integer('real_product_id')->nullable()->index();
            $table->boolean('is_available')->default(1)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_push_informations', function (Blueprint $table) {
            $table->dropIndex('is_added_from_csv');
            $table->dropIndex('real_product_id');
            $table->dropIndex('is_available');

            $table->dropColumn('is_added_from_csv');
            $table->dropColumn('real_product_id');
            $table->dropColumn('is_available');
        });
    }
}
