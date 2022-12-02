<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToGroupRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_routes', function (Blueprint $table) {
            $table->string('route_name')->nullable()->after('route_id');
            $table->string('domain')->nullable()->after('route_id');
            $table->string('url')->nullable()->after('route_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_routes', function (Blueprint $table) {
            //
        });
    }
}
