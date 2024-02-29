<?php

namespace Database\Seeders;

use App\Setting;
use Illuminate\Database\Seeder;

class AddSettingValueForThresholdMemoryUsesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::updateOrCreate([
            'name' => 'thresold_limit_for_memory_uses',
        ], [
            'val'  => 80,
            'type' => 'number',
        ]);
    }
}
