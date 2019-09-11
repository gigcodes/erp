<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSupplierCategoryIdToSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->integer('supplier_category_id')->unsigned()->nullable()->after('id');
            $table->foreign('supplier_category_id')->references('id')->on('supplier_category');
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
            $table->dropForeign(['supplier_category_id']);
            $table->dropColumn('supplier_category_id');
        });
    }
}
