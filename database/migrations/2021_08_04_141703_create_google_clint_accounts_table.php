<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleClintAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_client_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('GOOGLE_CLIENT_ID')->nullable();
            $table->string('GOOGLE_CLIENT_SECRET')->nullable();
            $table->string('GOOGLE_CLIENT_KEY')->nullable();
            $table->string('GOOGLE_CLIENT_APPLICATION_NAME')->nullable();
            $table->string('GOOGLE_CLIENT_ACCESS_TOKEN')->nullable();
            $table->string('GOOGLE_CLIENT_MULTIPLE_KEYS')->nullable();
            $table->string('py_facebook_script')->nullable();
            $table->string('py_crop_script')->nullable();
            $table->string('product_check_py')->nullable();
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('google_clint_accounts');
    }
}
