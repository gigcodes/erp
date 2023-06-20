<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSimulatorFiledForCustomerVendorSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function($table) {
            $table->dropColumn('is_auto_simulator');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->boolean("is_auto_simulator")->default(0)->nullable();
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean("is_auto_simulator")->default(0)->nullable();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->boolean("is_auto_simulator")->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function($table) {
            $table->boolean('is_auto_simulator');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn("is_auto_simulator");
        });
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn("is_auto_simulator");
        });
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn("is_auto_simulator");
        });
    }
}
