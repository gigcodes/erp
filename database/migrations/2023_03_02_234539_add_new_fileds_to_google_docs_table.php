<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFiledsToGoogleDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_docs', function (Blueprint $table) {
            $table->text('read')->nullable();
            $table->text('write')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_docs', function (Blueprint $table) {
            $table->dropColumn('read');
            $table->dropColumn('write');
        });
    }
}
