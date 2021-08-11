<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductCronSetupFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("products",function(Blueprint $table){
            $table->integer("is_cron_check")->default(0)->after("is_without_image");
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
        Schema::table("products",function(Blueprint $table){
            $table->dropField("is_cron_check");
        });
    }
}
