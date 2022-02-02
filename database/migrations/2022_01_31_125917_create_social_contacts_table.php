<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('social_config_id');
            $table->string('name')->nullable();
            $table->string('account_id', 50)->nullable();
            $table->tinyinteger('platform');
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
        Schema::dropIfExists('social_contacts');
    }
}
