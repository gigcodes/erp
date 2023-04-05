<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityInEmailCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_category', function (Blueprint $table) {
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW', 'UNDEFINED'])->nullable()->after('category_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_category', function (Blueprint $table) {
            //
        });
    }
}
