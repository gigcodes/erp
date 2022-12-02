<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReplyCategoryAddPushedToWatson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reply_categories', function (Blueprint $table) {
            $table->boolean('pushed_to_watson')->default(0);
            $table->integer('dialog_id')->nullable();
            $table->integer('intent_id')->nullable();
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
