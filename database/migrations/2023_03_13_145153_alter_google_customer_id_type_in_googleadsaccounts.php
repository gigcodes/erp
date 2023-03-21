<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGoogleCustomerIdTypeInGoogleadsaccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('googleadsaccounts', function (Blueprint $table) {
            $table->bigInteger('google_customer_id')->after('id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('googleadsaccounts', function (Blueprint $table) {
            $table->dropColumn('google_customer_id');
        });
    }
}
