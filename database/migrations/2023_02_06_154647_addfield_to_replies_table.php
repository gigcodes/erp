<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replies', function (Blueprint $table) {
            $exists = function (string $column) use ($table) {
                return (Schema::hasColumn($table->getTable(), $column));
            };
            $addUnlessExists = function (string $type, string $name, array $parameters = [])
            use ($table, $exists) {
                return $exists($name) ? null : $table->addColumn($type, $name, $parameters);
            };
            $dropIfExists = function (string $column) use ($table, $exists) {
                return $exists($column) ? $table->dropColumn($column) : null;
            };

            $dropIfExists('platform_id');
            $addUnlessExists('integer', 'platform_id');
        });

        Schema::table('replies', function ($table) {
            $table->integer('platform_id')->nullable();
        });

        Schema::table('translate_replies', function ($table) {
            $table->integer('platform_id')->nullable();
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
};