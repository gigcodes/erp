<?php

use Illuminate\Database\Migrations\Migration;

class AlterChangeColumnsTypeUsersFeedbackHrTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE `users_feedback_hr_tickets` CHANGE `feedback_cat_id` `feedback_cat_id` INT(11) NULL, 
                    CHANGE `user_id` `user_id` INT(11) NULL, 
                    CHANGE `task_subject` `task_subject` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, 
                    CHANGE `task_type` `task_type` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, 
                    CHANGE `repository_id` `repository_id` BIGINT(20) NULL, 
                    CHANGE `task_detail` `task_detail` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, 
                    CHANGE `cost` `cost` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, 
                    CHANGE `task_asssigned_to` `task_asssigned_to` BIGINT(20) NULL, 
                    CHANGE `status` `status` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
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
