<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLockColumnsInUicheckUserAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uicheck_user_accesses', function (Blueprint $table) {
            $table->tinyInteger('lock_developer')->default(1);
            $table->tinyInteger('lock_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uicheck_user_accesses', function (Blueprint $table) {
            $table->dropColumn('lock_developer');
            $table->dropColumn('lock_admin');
        });
    }
}
