<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_availabilities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('event_id');
            $table->tinyInteger('numeric_day');
            $table->time('start_at');
            $table->time('end_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_availabilities');
    }
}
