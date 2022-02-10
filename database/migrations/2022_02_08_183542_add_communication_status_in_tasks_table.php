<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommunicationStatusInTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('communication_status')->after('last_date_time_reminder')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    
}
