<?php

use Database\Seeders\LogListMagentoSyncStatusTableSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogListMagentoSyncStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_list_magento_sync_statuses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("name");
            $table->string("color")->nullable();
        });

        Artisan::call('db:seed', [
            '--class' => LogListMagentoSyncStatusTableSeeder::class
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_list_magento_sync_statuses');
    }
}