<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnBroadcastIdInImQueuesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('im_queues', function (Blueprint $table) {
            $table->integer('broadcast_id')->after('marketing_message_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('im_queues', function (Blueprint $table) {
            $table->integer('broadcast_id')->after('marketing_message_type_id')->nullable();
        });
    }
}
