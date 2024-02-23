<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\StoreWebsiteTableProjectUpdateSeeder;

class UpdateStoreWebsiteTableProjectSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => StoreWebsiteTableProjectUpdateSeeder::class,
        ]);
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
