<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCsvTransltorTableHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('csv_translator_history', function (Blueprint $table) {
            $table->string('status_en')->after('en')->nullable();
            $table->string('status_es')->after('es')->nullable();
            $table->string('status_ru')->after('ru')->nullable();
            $table->string('status_ko')->after('ko')->nullable();
            $table->string('status_ja')->after('ja')->nullable();
            $table->string('status_it')->after('it')->nullable();
            $table->string('status_de')->after('de')->nullable();
            $table->string('status_fr')->after('fr')->nullable();
            $table->string('status_nl')->after('nl')->nullable();
            $table->string('status_zh')->after('zh')->nullable();
            $table->string('status_ar')->after('ar')->nullable();
            $table->string('status_ur')->after('ur')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('csv_translator_history', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('status_en');
            $table->dropColumn('status_es');
            $table->dropColumn('status_ru');
            $table->dropColumn('status_ko');
            $table->dropColumn('status_ja');
            $table->dropColumn('status_it');
            $table->dropColumn('status_de');
            $table->dropColumn('status_fr');
            $table->dropColumn('status_nl');
            $table->dropColumn('status_zh');
            $table->dropColumn('status_ar');
            $table->dropColumn('status_ur');
        });
    }
}
