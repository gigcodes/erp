<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsProcessToMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->tinyInteger('is_processed')->default(0)->index()->comment('[0=>pending,1=>processed,2=>file-not-exists]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {

            $table->dropIndex(['is_processed']);
            $table->dropColumn('is_processed');
        });
    }
}
