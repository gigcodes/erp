<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInstagramColumnsToSocialPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_posts', function (Blueprint $table) {
            $table->json('media')->nullable();
            $table->string('permalink')->nullable();
            $table->json('custom_data')->nullable();
            $table->string('image_path', 360)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_posts', function (Blueprint $table) {
            $table->dropColumn('media');
            $table->dropColumn('permalink');
            $table->dropColumn('custom_data');
        });
    }
}
