<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select("
            CREATE TABLE `customer_charities` (
            `id` int(10) UNSIGNED NOT NULL,
            `category_id` int(10) UNSIGNED DEFAULT NULL,
            `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
            `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `default_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `whatsapp_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `social_handle` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `login` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `gst` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `account_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `account_iban` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `account_swift` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            `notes` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            `frequency` int(11) NOT NULL DEFAULT 0,
            `reminder_last_reply` int(11) NOT NULL DEFAULT 1,
            `reminder_from` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
            `reminder_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `has_error` tinyint(4) NOT NULL DEFAULT 0,
            `is_blocked` tinyint(4) NOT NULL DEFAULT 0,
            `updated_by` int(11) NOT NULL,
            `status` int(11) DEFAULT 1,
            `frequency_of_payment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `bank_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `ifsc_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `remark` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `chat_session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        DB::select('ALTER TABLE `customer_charities` ADD PRIMARY KEY(`id`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_charities');
    }
}
