<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldToScriptDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('', function (Blueprint $table) {
            //
        });

        Schema::table('script_documents', function (Blueprint $table) {
            $table->longText('description')->after('file')->nullable();
            $table->string('location')->after('comments')->nullable();
            $table->string('last_run')->after('author')->nullable();
            $table->string('status')->after('last_run')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('script_documents', function (Blueprint $table) {
            //
        });
    }
}
