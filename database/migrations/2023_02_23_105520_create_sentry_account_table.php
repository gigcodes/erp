<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sentry_accounts')) {
            Schema::create('sentry_accounts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('sentry_token');
                $table->string('sentry_organization');
                $table->string('sentry_project');
                $table->softDeletes();
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
        Schema::dropIfExists('sentry_accounts');
    }
};