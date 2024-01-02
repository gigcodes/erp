<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDialogflowEntityTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialogflow_entity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('display_name')->nullable();
            $table->string('kind')->nullable();
            $table->string('response_id')->nullable();
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
        Schema::dropIfExists('dialogflow_entity_types');
    }
}
