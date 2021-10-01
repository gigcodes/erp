<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMailingListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailinglists', function (Blueprint $table) {
            $table->boolean('is_master')->default(0);
            $table->boolean('is_spam')->default(0);
            $table->integer('emails_sent')->default(0);
            $table->string('spam_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
