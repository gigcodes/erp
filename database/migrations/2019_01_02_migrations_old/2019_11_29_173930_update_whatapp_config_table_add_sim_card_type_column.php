<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateWhatappConfigTableAddSimCardTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whatsapp_configs', function ($table) {
            $table->string('sim_card_type')->nullable()->after('simcard_owner');
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
            $table->dropColumn('sim_card_type');
        });
    }
}
