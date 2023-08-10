<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreWebsitesApiTokensHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_websites_api_tokens_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('store_websites_id');
            $table->string('old_api_token')->nullable();
            $table->string('new_api_token')->nullable();
            $table->integer('updatedBy');
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
        Schema::dropIfExists('store_websites_api_tokens_histories');
    }
}
