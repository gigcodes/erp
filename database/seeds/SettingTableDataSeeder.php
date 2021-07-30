<?php

use Illuminate\Database\Seeder;
use App\Setting;
class SettingTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            ['name' => 'posts_per_day'],
            ['name' => 'likes_per_day'],
            ['name' => 'send_requests_per_day'],
            ['name' => 'accept_requests_per_day'],
            ['name' => 'image_per_post'],
        ]);
    }
}
