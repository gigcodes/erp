<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\ConfigRefactorSectionTableSeeder;

class CreateTruncateSeederForConfigRefactorTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('db:seed', [
            '--class' => ConfigRefactorSectionTableSeeder::class,
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
