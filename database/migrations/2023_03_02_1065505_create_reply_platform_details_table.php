<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faq_platform_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reply_id')->nullable();
            $table->text('type')->nullable();
            $table->integer('store_website_id')->nullable();
            $table->text('store_code')->nullable();
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
        Schema::dropIfExists('faq_platform_details');
    }
};
