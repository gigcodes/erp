<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowLogMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flow_log_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flow_log_id')->unsigned()->index()->foreign()->references('id')->on('flow_logs')->onDelete('cascade');
            $table->text('messages')->nullable();
            $table->string('flow_action')->nullable();
            $table->string('modalType')->nullable();
            $table->string('customer_name', 200)->nullable();
            $table->text('leads')->nullable();
            $table->integer('store_website_id')->nullable();
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
        Schema::dropIfExists('flow_log_messages');
    }
}
