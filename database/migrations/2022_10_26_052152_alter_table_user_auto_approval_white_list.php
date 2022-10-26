<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUserAutoApprovalWhiteList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->integer("is_auto_approval")->default(1)->change();
            $table->integer("is_whitelisted")->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("users", function (Blueprint $table) {
            $table->integer("is_auto_approval")->default(0)->change();
            $table->integer("is_whitelisted")->default(0)->change();
        });
    }
}
