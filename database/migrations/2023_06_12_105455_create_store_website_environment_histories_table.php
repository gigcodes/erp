<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsiteEnvironmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_environment_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer("store_website_id");
            $table->string("key");
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_website_environment_histories');
    }
}
