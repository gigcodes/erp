<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsInStoreWebsiteEnvironmentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store_website_environment_histories', function (Blueprint $table) {
            $table->integer('environment_id')->after('updated_at')->nullable();
            $table->integer('updated_by')->after('store_website_id')->nullable();
            $table->text('command')->after('new_value')->nullable();
            $table->string('job_id')->after('command')->nullable();
            $table->string('status')->after('job_id')->nullable();
            $table->text('response')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store_website_environment_histories', function (Blueprint $table) {
            $table->dropColumn('environment_id');
            $table->dropColumn('updated_by');
            $table->dropColumn('command');
            $table->dropColumn('job_id');
            $table->dropColumn('status');
            $table->dropColumn('response');
        });
    }
}
