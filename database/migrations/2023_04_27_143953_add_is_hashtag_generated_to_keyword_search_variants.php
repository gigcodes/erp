<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsHashtagGeneratedToKeywordSearchVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keyword_search_variants', function (Blueprint $table) {
            $table->boolean('is_hashtag_generated')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keyword_search_variants', function (Blueprint $table) {
            $table->dropColumn('is_table_generated');
        });
    }
}
