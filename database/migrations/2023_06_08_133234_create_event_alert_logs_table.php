<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventAlertLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_alert_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('eventalertloggable_id');
            $table->string("eventalertloggable_type");
            $table->integer('user_id');
            $table->boolean('is_read');
            $table->dateTime('event_alert_date');
            $table->string('event_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_alert_logs');
    }
}
