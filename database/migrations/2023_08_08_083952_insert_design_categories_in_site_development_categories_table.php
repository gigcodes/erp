<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\SiteDevelopmentDesignCategoriesInsertSeeder;

class InsertDesignCategoriesInSiteDevelopmentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => SiteDevelopmentDesignCategoriesInsertSeeder::class,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
