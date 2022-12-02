<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockColsToUichecks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uichecks', function (Blueprint $table) {
            $table->tinyInteger('lock_developer')->index();
            $table->tinyInteger('lock_admin')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uichecks', function (Blueprint $table) {
            $table->dropColumn('lock_developer');
            $table->dropColumn('lock_admin');
        });
    }
}
