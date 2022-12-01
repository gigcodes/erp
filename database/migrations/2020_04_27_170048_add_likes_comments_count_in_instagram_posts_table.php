<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLikesCommentsCountInInstagramPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->integer('likes')->nullable()->after('source');
            $table->integer('comments_count')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instagram_posts', function (Blueprint $table) {
            $table->dropColumn('likes');
            $table->dropColumn('comments_count');
        });
    }
}
