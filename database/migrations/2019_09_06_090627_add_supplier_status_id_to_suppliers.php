<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierStatusIdToSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->integer('supplier_status_id')->unsigned()->nullable()->after('supplier_category_id');
            $table->foreign('supplier_status_id')->references('id')->on('supplier_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['supplier_status_id']);
            $table->dropColumn('supplier_status_id');
        });
    }
}
