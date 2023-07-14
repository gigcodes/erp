<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBuildProcessStatusHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE build_process_histories MODIFY status ENUM('SUCCESS', 'FAILURE', 'RUNNING', 'WAITING', 'UNSTABLE', 'ABORTED') NOT NULL");
        
        Schema::create('build_process_status_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->bigInteger('build_process_history_id');
            $table->bigInteger('build_number');
            $table->string('old_status')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('build_process_status_histories');
    }
}
