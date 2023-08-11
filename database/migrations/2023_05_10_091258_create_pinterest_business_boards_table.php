<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinterestBusinessBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_business_boards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinterest_ads_account_id');
            $table->string('board_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('privacy', ['PUBLIC', 'PROTECTED', 'SECRET'])->default('PUBLIC');
            $table->foreign('pinterest_ads_account_id')->references('id')->on('pinterest_ads_accounts')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('pinterest_business_boards');
    }
}
