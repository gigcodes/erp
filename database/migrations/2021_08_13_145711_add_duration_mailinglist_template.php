<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDurationMailinglistTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailinglist_templates', function (Blueprint $table) {
            $table->boolean('auto_send')->default(0);
            $table->string('duration');
            $table->string('duration_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mailinglist_templates', function (Blueprint $table) {
            //
        });
    }
}
