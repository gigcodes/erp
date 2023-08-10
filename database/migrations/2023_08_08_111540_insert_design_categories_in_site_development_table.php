<?php

use Illuminate\Database\Migrations\Migration;

class InsertDesignCategoriesInSiteDevelopmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => SiteDevelopmentDesignCategoriesUpdateSeeder::class,
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
