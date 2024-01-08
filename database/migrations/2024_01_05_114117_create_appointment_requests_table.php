<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('requested_user_id')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('request_status')->default(0)->comment('0 = requested, 1 = accepeted, 2 = declient');
            $table->datetime('requested_time')->nullable();
            $table->datetime('requested_time_end')->nullable();
            $table->integer('is_view')->default(0);
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
        Schema::dropIfExists('appointment_requests');
    }
}
