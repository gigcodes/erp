<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurringFieldsInEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(0);
            $table->string('recurring_end')->nullable();
            $table->date('end_date')->nullable()->change();
            $table->string('event_type', 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn("is_recurring");
            $table->dropColumn('recurring_end');
            $table->dropColumn('event_type');
        });
    }
}
