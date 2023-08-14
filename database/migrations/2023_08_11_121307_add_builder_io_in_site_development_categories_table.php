<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuilderIoInSiteDevelopmentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_development_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('builder_io')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_development_categories', function (Blueprint $table) {
            $table->dropColumn('builder_io');
        });
    }
}
