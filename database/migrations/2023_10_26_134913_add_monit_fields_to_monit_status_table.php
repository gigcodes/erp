<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonitFieldsToMonitStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('monit_status', function (Blueprint $table) {
            $table->longText('url')->after('memory')->nullable();
            $table->string('username')->after('url')->nullable();
            $table->string('password')->after('username')->nullable();
            $table->string('xmlid')->after('password')->nullable();
            $table->string('ip')->after('xmlid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('monit_status', function (Blueprint $table) {
            //
        });
    }
}
