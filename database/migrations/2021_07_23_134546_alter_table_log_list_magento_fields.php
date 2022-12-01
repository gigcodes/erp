<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableLogListMagentoFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('log_list_magentos', function (Blueprint $table) {
            $table->integer('error_condition')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('log_list_magentos', function (Blueprint $table) {
            $table->dropField('queue');
            $table->dropField('queue_id');
            $table->dropField('extra_attributes');
        });
    }
}
