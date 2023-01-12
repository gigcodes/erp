<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsvTranslatorHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('csv_translator_history')) {
            Schema::create('csv_translator_history', function (Blueprint $table) {
                $table->id();
                $table->integer('csv_translator_id');
                $table->string('key');
                $table->text('en');
                $table->text('es');
                $table->text('ru');
                $table->text('ko');
                $table->text('ja');
                $table->text('it');
                $table->text('de');
                $table->text('fr');
                $table->text('nl');
                $table->text('zh');
                $table->text('ar');
                $table->text('ur');
                $table->string('status')->default('unchecked')->comment('1.checked,2.unchecked');
                $table->integer('updated_by_user_id')->nullable();
                $table->integer('approved_by_user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('csv_translator_history', function (Blueprint $table) {
        });
    }
}
