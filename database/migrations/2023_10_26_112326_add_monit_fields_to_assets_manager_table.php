<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonitFieldsToAssetsManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets_manager', function (Blueprint $table) {
            $table->longText('monit_api_url')->after('active')->nullable();
            $table->string('monit_api_username')->after('monit_api_url')->nullable();
            $table->string('monit_api_password')->after('monit_api_username')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets_manager', function (Blueprint $table) {
            //
        });
    }
}
