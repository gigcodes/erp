<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StoreWebsites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_websites', function (Blueprint $table) {
            $table->string('logo_color')->after('cropping_size')->nullable();
            $table->string('logo_border_color')->after('logo_color')->nullable();
            $table->string('text_color')->after('logo_border_color')->nullable();
            $table->string('border_color')->after('text_color')->nullable();
            $table->string('border_thickness')->after('border_color')->nullable();
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
    }
}
