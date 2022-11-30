<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateSocialConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_configs', function ($table) {
            $table->string('page_id')->nullable();
            $table->string('page_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_configs', function ($table) {
            $table->dropColumn('page_id');
            $table->dropColumn('user_token');
        });
    }
}
