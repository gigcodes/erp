<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFiveFieldsInPostmanRequestCreatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->text('grumphp_errors')->nullable();
            $table->text('magento_api_standards')->nullable();
            $table->text('swagger_doc_block')->nullable();
            $table->string('used_for')->nullable();
            $table->string('user_in')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postman_request_creates', function (Blueprint $table) {
            $table->dropColumn('grumphp_errors');
            $table->dropColumn('magento_api_standards');
            $table->dropColumn('swagger_doc_block');
            $table->dropColumn('used_for');
            $table->dropColumn('user_in');
        });
    }
}
