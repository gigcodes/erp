<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexerStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('indexer_state', function (Blueprint $table) {
            $table->id();
            $table->string('index');
            $table->string('status');
            $table->text('settings');
            $table->text('logs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('indexer_state', function (Blueprint $table) {
            //
        });
    }
}
