<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidToStoreWebsiteYoutubeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_youtubes', function (Blueprint $table) {
            $table->id();
            $table->string('access_token', 1024)->nullable();
            $table->string('refresh_token', 1024)->nullable();
            $table->unsignedBigInteger('store_website_id')->nullable();
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
        Schema::table('store_website_youtubes', function (Blueprint $table) {
            Schema::dropIfExists('store_website_youtubes');
        });
    }
}
