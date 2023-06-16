<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreWebsiteEnvironmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_website_environments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer("store_website_id");
            $table->json('env_data')->nullable();
        });

        Artisan::call('db:seed', [
            '--class' => StoreWebsiteEnvironmentTableSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_website_environments');
    }
}
