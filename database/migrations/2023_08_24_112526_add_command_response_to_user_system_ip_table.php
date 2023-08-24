<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('status')->nullable();
            $table->text('message')->nullable();
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
            $table->dropColumn('status');
            $table->dropColumn('message');
        });
    }
}
