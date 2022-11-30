<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBacklinkIndexedPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backlink_indexed_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_website_id');
            $table->integer('tool_id');
            $table->text('database');
            $table->text('source_url');
            $table->text('source_title');
            $table->integer('response_code');
            $table->integer('backlinks_num');
            $table->integer('domains_num');
            $table->integer('last_seen');
            $table->integer('external_num');
            $table->integer('internal_num');
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
        Schema::dropIfExists('domain_indexed_pages');
    }
}
