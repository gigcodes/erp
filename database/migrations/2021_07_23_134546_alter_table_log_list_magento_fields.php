<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableLogListMagentoFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('log_list_magentos', function (Blueprint $table) {
            $table->string("queue")->nullable()->after("product_id");
            $table->string("queue_id")->nullable()->after("queue");
            $table->text("extra_attributes")->nullable()->after("queue_id");
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
        Schema::table('log_list_magentos', function (Blueprint $table) {
            $table->dropField("queue");
            $table->dropField("queue_id");
            $table->dropField("extra_attributes");
        });
    }
}
