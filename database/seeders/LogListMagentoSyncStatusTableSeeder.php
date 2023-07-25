<?php
namespace Database\Seeders;

use App\Loggers\LogListMagentoSyncStatus;
use Illuminate\Database\Seeder;

class LogListMagentoSyncStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $lists = [
            "success", 
            "error", 
            "waiting", 
            "started_push", 
            "size_chart_needed", 
            "image_not_found", 
            "translation_not_found"
        ];

        foreach($lists as $list)
        {
            LogListMagentoSyncStatus::firstOrCreate([
                'name' => $list
            ]);
        }
    }
}
