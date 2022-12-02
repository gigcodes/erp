<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapPythonLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('scrap_python_logs')) {
            Schema::create('scrap_python_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('website')->nullable();
                $table->string('date')->nullable();
                $table->string('device')->nullable();
                $table->text('log_text')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scrap_python_logs');
    }
}
