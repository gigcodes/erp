<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTaskHistoryForStartDate extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('task_history_for_start_date', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('task_id')->nullable()->index();
            $table->string('task_type')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->timestamp('old_value')->nullable();
            $table->timestamp('new_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('task_history_for_start_date');
    }
}
