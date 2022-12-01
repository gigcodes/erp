<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerPriorityPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_priority_points', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id')->index()->nullable();
            $table->string('website_base_priority')->default(0);
            $table->string('lead_points')->default(0);
            $table->string('order_points')->default(0);
            $table->string('refund_points')->default(0);
            $table->string('ticket_points')->default(0);
            $table->string('return_points')->default(0);
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
        Schema::dropIfExists('customer_priority');
    }
}
