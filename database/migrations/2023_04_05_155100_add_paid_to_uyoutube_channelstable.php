<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidToUyoutubeChannelstable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('youtube_channels', function (Blueprint $table) {
            $table->id();
            $table->string('store_websites', 128)->nullable();
            $table->string('status', 32)->nullable();
            $table->string('email', 64)->nullable();
            $table->string('oauth2_client_secret', 1024)->nullable();
            $table->string('access_token', 1024)->nullable();
            $table->string('oauth2_refresh_token', 1024)->nullable();
            $table->string('chanelId', 1024)->nullable();
            $table->string('chanel_name', 256)->nullable();
            $table->int('video_count', 11)->nullable();
            $table->int('subscribe_count', 11)->nullable();
            $table->timestamp('token_expire_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('youtube_channels', function (Blueprint $table) {
            Schema::dropIfExists('youtube_channels');
        });
    }
}
