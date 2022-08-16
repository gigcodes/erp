<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskHistoryForCost extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('task_history_for_cost', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('task_id')->nullable()->index();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('task_history_for_cost');
    }
}
