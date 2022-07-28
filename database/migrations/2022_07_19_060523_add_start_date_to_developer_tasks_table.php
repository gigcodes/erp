<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDateToDeveloperTasksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->timestamp('start_date')->nullable()->after('estimate_minutes')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('developer_tasks', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
}
