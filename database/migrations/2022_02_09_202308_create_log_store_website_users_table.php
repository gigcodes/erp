<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogStoreWebsiteUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('log_store_website_users')) {
            Schema::create('log_store_website_users', function (Blueprint $table) {
                $table->increments('id');
                $table->String('log_case_id');
                $table->String('store_website_id');
                $table->String('username');
                $table->String('useremail');
                $table->String('password');
                $table->String('first_name');
                $table->String('last_name');
                $table->String('website_mode');
                $table->String('log_msg');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_store_website_users');
    }
}
