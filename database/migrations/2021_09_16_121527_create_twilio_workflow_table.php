<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_workflows', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('twilio_credential_id')->nullable();
			$table->integer('twilio_workspace_id')->nullable();
			$table->string('workflow_name')->nullable();
			$table->string('workflow_sid')->nullable();
			$table->string('fallback_assignment_callback_url');
			$table->string('assignment_callback_url');
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
        Schema::dropIfExists('twilio_workflow');
    }
}
