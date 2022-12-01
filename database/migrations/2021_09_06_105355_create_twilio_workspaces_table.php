<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioWorkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_workspaces', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('twilio_credential_id')->nullable();
            $table->string('workspace_name')->nullable();
            $table->string('workspace_sid')->nullable();
            $table->text('workspace_response')->nullable();
            $table->integer('deleted')->default(0)->nullable();
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
        Schema::dropIfExists('twilio_workspaces');
    }
}
