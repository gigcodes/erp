<?php

use Illuminate\Database\Migrations\Migration;

class AddStatusColorInTaskstatusTableStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task_statuses', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->string('task_color')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
