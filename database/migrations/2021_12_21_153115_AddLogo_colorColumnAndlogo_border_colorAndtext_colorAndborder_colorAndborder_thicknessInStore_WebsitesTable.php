<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogo_colorColumnAndlogo_border_colorAndtext_colorAndborder_colorAndborder_thicknessInStore_WebsitesTable extends Migration
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
        
    }
}
