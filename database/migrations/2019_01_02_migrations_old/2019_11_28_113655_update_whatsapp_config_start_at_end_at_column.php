<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateWhatsappConfigStartAtEndAtColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whatsapp_configs', function ($table) {
            $table->integer('send_start');
            $table->integer('send_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('whatsapp_configs', function ($table) {
            $table->dropColumn('send_start');
            $table->dropColumn('send_end');
        });
    }
}
