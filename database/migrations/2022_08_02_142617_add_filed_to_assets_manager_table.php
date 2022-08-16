<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFiledToAssetsManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets_manager', function (Blueprint $table) {
            $table->string('server_password')->nullable();
            $table->longText('folder_name')->nullable();

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
            $table->dropColumn('server_password');
            $table->dropColumn('server_update');
        });
    }
}
