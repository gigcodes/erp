<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableStoreWebsiteProductAttributesFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('store_website_product_attributes', function (Blueprint $table) {
            $table->timestamp('uploaded_date')->default('0000-00-00 00:00:00')->after('store_website_id');
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
        Schema::table('store_website_product_attributes', function (Blueprint $table) {
            $table->dropField('uploaded_date');
        });
    }
}
