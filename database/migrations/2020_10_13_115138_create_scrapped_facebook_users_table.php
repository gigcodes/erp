<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrappedFacebookUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrapped_facebook_users', function (Blueprint $table) {
            $table->increments('id');
            $table->text('url')->nullable();
            $table->String('owner')->nullable();
            $table->text('bio')->nullable();
            $table->text('keyword')->nullable();
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
        Schema::dropIfExists('scrapped_facebook_users');
    }
}
