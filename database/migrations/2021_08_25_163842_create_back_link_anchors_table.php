<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackLinkAnchorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_link_anchors', function (Blueprint $table) {
            $table->increments('id');
            $table->text('store_website_id');
            $table->text('tool_id');
            $table->text('database');
            $table->text('anchor');
            $table->text('domains_num');
            $table->text('backlinks_num');
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
        Schema::dropIfExists('back_link_anchors');
    }
}
