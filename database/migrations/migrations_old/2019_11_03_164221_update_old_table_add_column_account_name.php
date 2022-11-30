<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateOldTableAddColumnAccountName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('old', function ($table) {
            $table->string('account_name')->nullable()->after('gst');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('old', function ($table) {
            $table->dropIfExists('account_name');
        });
    }
}
