<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinterestBusinessAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinterest_business_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('pinterest_client_id');
            $table->text('pinterest_client_secret');
            $table->string('pinterest_application_name');
            $table->boolean('is_active');
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
        Schema::dropIfExists('pinterest_business_accounts');
    }
}
