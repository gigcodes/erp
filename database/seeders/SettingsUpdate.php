<?php

namespace Database\Seeders;

use App\Account;
use App\Setting;
use Illuminate\Database\Seeder;

class SettingsUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $instaAccounts      = Account::where('status', 1)->where('platform', 'instagram')->get()->pluck('id')->toArray();
        $accountSettingInfo = Setting::where('name', 'instagram_message_queue_rate_setting')->first();
        if (empty($accountSettingInfo)) {
            $accountSettingInfo       = new Setting();
            $accountSettingInfo->name = 'instagram_message_queue_rate_setting';
        }
        $data = [];
        foreach ($instaAccounts as $acc) {
            $data[$acc] = 5;
        }
        $accountSettingInfo->val  = json_encode($data);
        $accountSettingInfo->type = 'str';
        $accountSettingInfo->save();
    }
}
