<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateQuickSellGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quick_sell_groups', function ($table) {
            $table->string('name')->nullable();
            $table->string('suppliers')->nullable();
            $table->string('brands')->nullable();
            $table->string('price')->nullable();
            $table->string('special_price')->nullable();
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
