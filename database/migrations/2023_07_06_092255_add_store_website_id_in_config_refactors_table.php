<?php

use App\ConfigRefactor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreWebsiteIdInConfigRefactorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config_refactors', function (Blueprint $table) {
            $table->integer('store_website_id')->after('updated_at')->nullable();
        });

        // Update all the old records to store_website_id = 9 (Brands & Labels)
        ConfigRefactor::whereNull('store_website_id')->update([
            'store_website_id' => 9,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('config_refactors', function (Blueprint $table) {
            $table->dropColumn('store_website_id');
        });
    }
}
