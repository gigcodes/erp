<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMonetryAccountHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('monetary_account_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('model_id')->index();
            $table->string('model_type');
            $table->double('amount')->default(0.00);
            $table->text('note')->nullable();
            $table->integer('monetary_account_id');
            $table->integer('user_id');
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
        Schema::dropIfExists('monetary_account_histories');
    }
}
