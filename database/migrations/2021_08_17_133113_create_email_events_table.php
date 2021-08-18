<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->int('customer_id');
            $table->int('list_contact_id');
            $table->int('template_id');
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
