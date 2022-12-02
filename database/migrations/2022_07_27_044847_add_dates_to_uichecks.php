<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesToUichecks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uichecks', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable();
            $table->timestamp('expected_completion_time')->nullable();
            $table->timestamp('actual_completion_time')->nullable();
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
            $table->dropColumn('start_time');
            $table->dropColumn('expected_completion_time');
            $table->dropColumn('actual_completion_time');
        });
    }
}
