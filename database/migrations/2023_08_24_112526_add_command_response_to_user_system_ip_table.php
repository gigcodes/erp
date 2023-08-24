<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommandResponseToUserSystemIpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_system_ip', function (Blueprint $table) {
            $table->text('command')->nullable();
            $table->text('response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_system_ip', function (Blueprint $table) {
            $table->dropColumn('command');
            $table->dropColumn('response');
        });
    }
}
