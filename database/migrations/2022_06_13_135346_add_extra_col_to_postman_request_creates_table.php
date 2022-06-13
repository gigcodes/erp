<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraColToPostmanRequestCreatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->text('controller_name')->nullable();
            $table->string('method_name')->nullable();
            $table->text('Remark')->nullable();
            $table->text('collection')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->dropColumn('controller_name');
            $table->dropColumn('method_name');
            $table->dropColumn('Remark');
            $table->dropColumn('collection');
        });
    }
}
