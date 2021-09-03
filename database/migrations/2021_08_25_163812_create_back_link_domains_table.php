<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackLinkDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_link_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->text('store_website_id');
            $table->text('tool_id');
			$table->enum('subtype', ['organic', 'paid']);
            $table->text('database');
            $table->text('domain');
            $table->text('domain_ascore');
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
        Schema::dropIfExists('back_link_domains');
    }
}
