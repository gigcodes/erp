<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoogleBigQueryDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_big_query_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('google_project_id')->nullable();
            $table->string('platform')->nullable();
            $table->string('bundle_identifier')->nullable();
            $table->string('event_id')->nullable();
            $table->string('is_fatal')->nullable();
            $table->string('issue_id')->nullable();
            $table->string('issue_title')->nullable();
            $table->string('issue_subtitle')->nullable();
            $table->string('event_timestamp')->nullable();
            $table->string('received_timestamp')->nullable();
            $table->json('device')->nullable();
            $table->json('memory')->nullable();
            $table->json('storage')->nullable();
            $table->json('operating_system')->nullable();
            $table->json('application')->nullable();
            $table->string('user')->nullable();
            $table->json('custom_keys')->nullable();
            $table->text('installation_uuid')->nullable();
            $table->string('crashlytics_sdk_version')->nullable();
            $table->string('app_orientation')->nullable();
            $table->string('device_orientation')->nullable();
            $table->string('process_state')->nullable();
            $table->json('logs')->nullable();
            $table->json('breadcrumbs')->nullable();
            $table->json('blame_frame')->nullable();
            $table->json('exceptions')->nullable();
            $table->json('errors')->nullable();
            $table->json('threads')->nullable();
            $table->integer('website_id')->nullable();
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
        Schema::dropIfExists('google_big_query_datas');
    }
}
