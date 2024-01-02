<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApprovedByUserIdFieldInNewslettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->integer('approved_by_user_id')->nullable()->after('translated_from');
            $table->boolean('is_flagged_translation')->default(0)->nullable()->after('approved_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn('approved_by_user_id');
            $table->dropColumn('is_flagged_translation');
        });
    }
}
