<?php

use App\Models\ThemeStructure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRootColumnInThemeStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('theme_structure', function (Blueprint $table) {
            $table->boolean('is_root')->after('parent_id')->default(false);
        });

        ThemeStructure::create([
            'name' => 'Root Folder',
            'is_file' => 0,
            'is_root' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theme_structure', function (Blueprint $table) {
            //
        });
    }
}
