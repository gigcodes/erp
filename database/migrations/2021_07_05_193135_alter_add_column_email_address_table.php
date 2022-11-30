<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddColumnEmailAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_addresses', function (Blueprint $table) {
            $table->string('recovery_phone')->after('from_address')->nullable();
            $table->string('recovery_email')->after('recovery_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_addresses', function (Blueprint $table) {
            $table->dropColumn('recovery_phone');
            $table->dropColumn('recovery_email');
        });
    }
}
