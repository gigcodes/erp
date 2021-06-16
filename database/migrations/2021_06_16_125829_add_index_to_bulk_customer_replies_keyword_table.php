<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToBulkCustomerRepliesKeywordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_customer_replies_keywords', function (Blueprint $table) {

            $table->string('value','512')->change();
            $table->index(['count','value']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_customer_replies_keywords', function (Blueprint $table) {
            $table->text('value')->change();

            $table->dropIndex(['count','value']);

        });
    }
}
