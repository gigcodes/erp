<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVncFieldsToAssetsManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets_manager', function (Blueprint $table) {
            $table->string('vnc_ip')->after('monit_api_password')->nullable();
            $table->string('vnc_port')->after('vnc_ip')->nullable();
            $table->string('vnc_password')->after('vnc_port')->nullable();
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
