<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentReviewIdTasks extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_review_task_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('parent_review_task_id');
        });
    }
}
