<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActualStartendTimeToDeveloperTasks extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->timestamp('actual_start_date')->nullable();
            $table->timestamp('actual_end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->dropColumn('actual_start_date');
            $table->dropColumn('actual_end_date');
        });
    }
}
