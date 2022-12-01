<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('list_contact_id');
            $table->integer('template_id');
            $table->boolean('sent')->default(0);
            $table->boolean('delivered')->default(0);
            $table->boolean('opened')->default(0);
            $table->boolean('spam')->default(0);
            $table->datetime('spam_date')->nullable();
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
        Schema::dropIfExists('email_events');
    }
}
