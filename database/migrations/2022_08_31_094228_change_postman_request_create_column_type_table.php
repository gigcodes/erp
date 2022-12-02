<?php

use Illuminate\Database\Migrations\Migration;

class ChangePostmanRequestCreateColumnTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE postman_multiple_urls MODIFY COLUMN request_url TEXT');

        DB::statement('ALTER TABLE postman_request_creates MODIFY COLUMN request_url TEXT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE postman_multiple_urls MODIFY COLUMN request_url STRING');

        DB::statement('ALTER TABLE postman_request_creates MODIFY COLUMN request_url STRING');
    }
}
