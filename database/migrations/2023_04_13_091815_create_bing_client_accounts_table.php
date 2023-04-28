<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBingClientAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bing_client_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bing_client_id', 191);
            $table->string('bing_client_secret', 191);
            $table->string('bing_client_key', 191)->nullable();
            $table->string('bing_client_application_name', 191)->nullable();
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
        Schema::dropIfExists('bing_client_accounts');
    }
}
