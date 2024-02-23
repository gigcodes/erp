<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('social_contact_id');
            $table->json('from');
            $table->json('to');
            $table->string('message');
            $table->json('reactions')->nullable();
            $table->boolean('is_unsupported');
            $table->string('message_id');
            $table->dateTime('created_time');
            $table->json('attachments')->nullable();
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
        Schema::dropIfExists('social_messages');
    }
}
