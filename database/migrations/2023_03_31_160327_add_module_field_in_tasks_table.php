<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModuleFieldInTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(env('TOTEM_TABLE_PREFIX','').'tasks', function (Blueprint $table) {
            $table->integer('developer_module_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(env('TOTEM_TABLE_PREFIX','').'tasks', function (Blueprint $table) {
            $table->dropColumn('developer_module_id');
        });
    }
}
