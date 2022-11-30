<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnScraperImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraper_imags', function ($table) {
            $table->longText('coordinates')->nullable();
            $table->integer('height')->nullable();
            $table->integer('width')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('scraper_imags', function (Blueprint $table) {
            $table->dropField('coordinates');
            $table->dropField('height');
            $table->dropField('width');
        });
    }
}
