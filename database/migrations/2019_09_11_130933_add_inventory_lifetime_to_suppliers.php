<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInventoryLifetimeToSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table( 'suppliers', function ( $table ) {
            $table->integer( 'inventory_lifetime' )->default(2)->after( 'scraper_name' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( 'suppliers', function ( $table ) {
            $table->dropColumn( 'inventory_lifetime' );
        } );
    }
}
