<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleadsaccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('googleadsaccounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_name', 55);
            $table->string('store_websites', 55);
            $table->string('config_file_path')->comment('it is basically will be adsapi_php.ini');
            $table->string('notes');
            $table->string('status', 15);
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
        Schema::dropIfExists('googleadsaccounts');
    }
}
