<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSiteAssetsInSiteDevelopmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_developments', function (Blueprint $table) {
            //
            $table->tinyInteger('is_site_asset')->default(0);
        });
    } 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_developments', function (Blueprint $table) {
            //
            $table->dropColumn('is_site_asset');
        });
    }
}
