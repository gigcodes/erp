<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableGoogleTranslationSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('google_traslation_settings', function (Blueprint $table) {
            $table->timestamp('last_error_at')->nullable()->after('last_note');
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
        Schema::table('google_traslation_settings', function (Blueprint $table) {
            $table->dropField('last_error_at');
        });
    }
}
