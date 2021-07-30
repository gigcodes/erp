<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCategorySizeChartNeeded extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("categories",function(Blueprint $table) {
            $table->integer("size_chart_needed")->default(0)->after("need_to_check_size");
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
        Schema::table("categories",function(Blueprint $table) {
            $table->dropField("size_chart_needed");
        });
    }
}
