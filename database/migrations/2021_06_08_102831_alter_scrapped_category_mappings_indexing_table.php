<?php

use App\ScrappedCategoryMapping;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScrappedCategoryMappingsIndexingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        ScrappedCategoryMapping::truncate();

        Schema::table('scrapped_category_mappings', function (Blueprint $table) {
            $table->index('name');
            $table->unique('name');
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
