<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddDefaultAccountColumnGoogleDialogAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_dialog_accounts', function ($table) {
            $table->boolean('default_selected')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_dialog_accounts', function ($table) {
            $table->dropColumn('default_selected');
        });
    }
}
