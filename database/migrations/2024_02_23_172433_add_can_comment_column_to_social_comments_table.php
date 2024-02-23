<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanCommentColumnToSocialCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->boolean('can_comment')->default(true);
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_comments', function (Blueprint $table) {
            $table->dropColumn('can_comment');
        });
    }
}
