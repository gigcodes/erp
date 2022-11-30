<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateLivechatColumnAddUsernameAddKeyColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('livechatinc_settings', function ($table) {
            $table->string('username')->nullable();
            $table->text('key')->nullable();
            $table->dropColumn('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('livechatinc_settings', function ($table) {
            $table->dropColumn('username');
            $table->dropColumn('key');
        });
    }
}
