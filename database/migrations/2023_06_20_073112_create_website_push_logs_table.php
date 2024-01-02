<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebsitePushLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_push_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('websitepushloggable_id');
            $table->string('websitepushloggable_type');
            $table->string('type');
            $table->string('name');
            $table->text('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('website_push_logs');
    }
}
