<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableScraperRemarksExtraFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('scrap_remarks', function (Blueprint $table) {
            $table->string('old_value')->nullable()->after('remark');
            $table->string('new_value')->nullable()->after('old_value');
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
        Schema::table('scrap_remarks', function (Blueprint $table) {
            $table->dropField('old_value');
            $table->dropField('new_value');
        });
    }
}
