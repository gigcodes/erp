<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScraperNameToSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'suppliers', function ( $table ) {
            $table->string( 'scraper_name' )->nullable()->after( 'reminder_message' );
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
            $table->dropColumn( 'scraper_name' );
        } );
    }
}
