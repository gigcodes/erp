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
        Schema::create('social_ad_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_website_id');
            $table->string('name');
            $table->string('ad_account_id');
            $table->string('page_token');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('social_ad_accounts');
    }
};
