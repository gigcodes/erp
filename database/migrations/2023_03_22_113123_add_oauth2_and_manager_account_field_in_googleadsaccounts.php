<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOauth2AndManagerAccountFieldInGoogleadsaccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('googleadsaccounts', function (Blueprint $table) {
            $table->string('google_adwords_client_account_email')->nullable();
            $table->string('google_adwords_client_account_password')->nullable();
            $table->unsignedBigInteger('google_adwords_manager_account_customer_id')->nullable();
            $table->string('google_adwords_manager_account_email')->nullable();
            $table->string('google_adwords_manager_account_password')->nullable();
            $table->string('google_adwords_manager_account_developer_token')->nullable();
            $table->text('oauth2_client_id')->nullable();
            $table->text('oauth2_client_secret')->nullable();
            $table->text('oauth2_refresh_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('googleadsaccounts', function (Blueprint $table) {
            //
        });
    }
}
