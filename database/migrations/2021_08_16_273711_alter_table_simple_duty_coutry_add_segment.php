<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSimpleDutyCoutryAddSegment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('simply_duty_countries', function (Blueprint $table) {
            $table->integer('segment_id')->default(0)->nullable()->after('default_duty');
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
        Schema::table('simply_duty_countries', function (Blueprint $table) {
            $table->dropField('segment_id');
        });
    }
}
