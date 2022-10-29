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
        if (!Setting::get('posts_per_day')) {
            Setting::insert([
                'name' => 'posts_per_day',
                'val' => 1,
            ]);
        }
        if (!Setting::get('send_requests_per_day')) {
            Setting::insert([
                'name' => 'send_requests_per_day',
                'val' => 1,
            ]);
        }
        if (!Setting::get('likes_per_day')) {
            Setting::insert([
                'name' => 'likes_per_day',
                'val' => 1,
            ]);
        }
        if (!Setting::get('accept_requests_per_day')) {
            Setting::insert([
                'name' => 'accept_requests_per_day',
                'val' => 1,
            ]);
        }
        if (!Setting::get('image_per_post')) {
            Setting::insert([
                'name' => 'image_per_post',
                'val' => 1,
            ]);
        }
        if (!Setting::get('log_apis')) {
            Setting::insert([
                'name' => 'log_apis',
                'val' => 1,
                'type' => 'tinyint'
            ]);
        }
    }
}
