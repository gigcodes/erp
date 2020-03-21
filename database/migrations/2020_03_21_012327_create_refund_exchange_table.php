<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_exchanges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->index();
            $table->enum('type', ['refund', 'exchange']);
            $table->integer('status');
            $table->text('pickup_address')->nullable();
            $table->text('remarks');
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
        Schema::dropIfExists('return_exchanges');
    }
}
