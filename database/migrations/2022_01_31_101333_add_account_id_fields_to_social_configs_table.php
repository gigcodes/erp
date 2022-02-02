<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountIdFieldsToSocialConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_configs', function (Blueprint $table) {
            $table->string('account_id')->nullable();
            $table->text('webhook_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_configs', function (Blueprint $table) {
            $table->dropColumn(['account_id','webhook_token']);
        });
    }
}

