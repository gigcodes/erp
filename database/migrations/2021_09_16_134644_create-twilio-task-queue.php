<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwilioTaskQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_task_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('twilio_credential_id')->nullable();
            $table->integer('twilio_workspace_id')->nullable();
            $table->string('task_queue_name')->nullable();
            $table->enum('task_order', ['FIFO', 'LIFO']);
            $table->string('task_queue_sid')->nullable();
            $table->integer('reservation_activity_id')->nullable();
            $table->integer('assignment_activity_id')->nullable();
            $table->integer('max_reserved_workers')->nullable();
            $table->text('target_workers')->nullable();
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
        //
    }
}
