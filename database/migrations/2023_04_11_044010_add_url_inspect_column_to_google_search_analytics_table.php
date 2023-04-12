<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlInspectColumnToGoogleSearchAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_search_analytics', function (Blueprint $table) {
            $table->boolean('indexed')->nullable()->after('date');
            $table->boolean('not_indexed')->nullable()->after('date');
            $table->string('not_indexed_reason')->nullable()->after('date');
            $table->boolean('mobile_usable')->nullable()->after('date');
            $table->text('enhancements')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_search_analytics', function (Blueprint $table) {
            $table->dropColumn('indexed');
            $table->dropColumn('not_indexed');
            $table->dropColumn('not_indexed_reason');
            $table->dropColumn('mobile_usable');
            $table->dropColumn('enhancements');
        });
    }
}
