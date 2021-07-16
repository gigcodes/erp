<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCroppedImageReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('cropped_image_references',function(Blueprint $table) {
            $table->string('instance_id')->nullable()->after('speed');
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
        Schema::table('cropped_image_references',function(Blueprint $table) {
            $table->dropField('instance_id');
        });
    }
}
