<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedProductAttributeJobFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->Integer('updated_attribute_job_status')->default(0)->after('is_push_attempted')->comment('0- pending, 1-success,2- failed');
            $table->Integer('updated_attribute_job_attempt_count')->default(0)->after('updated_attribute_job_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('updated_attribute_job_status');
            $table->dropColumn('updated_attribute_job_attempt_count');
        });
    }
}
