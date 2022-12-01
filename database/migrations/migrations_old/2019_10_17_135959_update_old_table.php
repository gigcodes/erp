<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateOldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('old', function ($table) {
            $table->integer('is_blocked')->default(0);
            $table->string('phone');
            $table->string('gst')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_iban')->nullable();
            $table->string('account_swift')->nullable();
            $table->integer('category_id');
            $table->string('pending_payment');
            $table->string('currency');
            $table->integer('is_payable')->default(0);
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
