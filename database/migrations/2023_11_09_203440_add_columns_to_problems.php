<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToProblems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->string('datetime')->nullable();
            $table->string('recovery_time')->nullable();
            $table->string('severity')->nullable();
            $table->string('host')->nullable();
            $table->string('problem')->nullable();
            $table->string('time_duration')->nullable();
            $table->boolean('acknowledged')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problems', function (Blueprint $table) {
            $table->dropColumn('datetime');
            $table->dropColumn('recovery_time');
            $table->dropColumn('severity');
            $table->dropColumn('host');
            $table->dropColumn('problem');
            $table->dropColumn('time_duration');
            $table->dropColumn('acknowledged');
        });
    }
}
