<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScriptsExecutionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scripts_execution_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('script_document_id')->unsigned();
            $table->longText('description')->nullable();
            $table->string('run_time', 191)->nullable();
            $table->longText('run_output')->nullable();
            $table->string('run_status', 191)->nullable();
            $table->timestamps(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scripts_execution_histories');
    }
}
