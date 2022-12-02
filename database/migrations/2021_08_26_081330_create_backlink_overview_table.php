<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBacklinkOverviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backlink_overview', function (Blueprint $table) {
            $table->increments('id');
            $table->text('store_website_id');
            $table->text('tool_id');
            $table->text('database');
            $table->text('ascore');
            $table->text('total');
            $table->text('domains_num');
            $table->text('urls_num');
            $table->text('ips_num');
            $table->text('ipclassc_num');
            $table->text('follows_num');
            $table->text('nofollows_num');
            $table->text('sponsored_num');
            $table->text('ugc_num');
            $table->text('texts_num');
            $table->text('images_num');
            $table->text('forms_num');
            $table->text('frames_num');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('backlink_overview');
    }
}
