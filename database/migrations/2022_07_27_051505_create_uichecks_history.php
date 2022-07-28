<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUichecksHistory extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('uichecks_hisotry', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('uichecks_id')->index();
            $table->string('type', 30)->index();
            $table->string('old_val', 255)->nullable();
            $table->string('new_val', 255)->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('uichecks_hisotry');
    }
}
