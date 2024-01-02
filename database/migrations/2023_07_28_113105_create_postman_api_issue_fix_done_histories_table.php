<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmanApiIssueFixDoneHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postman_api_issue_fix_done_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('postman_create_id');
            $table->integer('old_value')->nullable();
            $table->integer('new_value')->nullable();
            $table->integer('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postman_api_issue_fix_done_histories');
    }
}
