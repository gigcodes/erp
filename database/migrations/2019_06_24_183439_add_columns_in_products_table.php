<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInProductsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('listing_rejected_by')->nullable();
            $table->date('listing_rejected_on')->nullable();
            $table->boolean('is_corrected')->default(0);
            $table->boolean('is_script_corrected')->default(0);
            $table->boolean('is_authorized')->default(0);
            $table->integer('authorized_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['listing_rejected_by', 'listing_rejected_on', 'is_corrected', 'is_script_corrected', 'is_authorized', 'authorized_by']);
        });
    }
}
