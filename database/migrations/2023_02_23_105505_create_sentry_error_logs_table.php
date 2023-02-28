<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSentryErrorLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentry_error_logs', function (Blueprint $table) {
            $table->increments('id')->nullable();
            $table->integer('error_id')->nullable();
            $table->text('error_title')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('issue_category')->nullable();
            $table->boolean('is_unhandled')->default(0);
            $table->datetime('first_seen')->nullable();
            $table->datetime('last_seen')->nullable();
            $table->integer('project_id')->nullable();
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
        Schema::dropIfExists('sentry_error_logs');
    }
}
