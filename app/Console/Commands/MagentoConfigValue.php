<?php

namespace App\Console\Commands;

use App\LogRequest;
use App\StoreWebsite;
use App\MagentoSetting;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class MagentoConfigValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magento:get-config-value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting value from magento config and update in magento setting table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        $magentoSettings = MagentoSetting::leftJoin('users', 'magento_settings.created_by', 'users.id');
        $magentoSettings->select('magento_settings.*', 'users.name as uname');
        $magentoSettings = $magentoSettings->orderBy('magento_settings.created_at', 'DESC')->get();

        $data = $magentoSettings;
        $data = $data->groupBy('store_website_id');
        $newValues = [];
        foreach ($data as $websiteId => $settings) {
            $websiteUrl = StoreWebsite::where('id', $websiteId)->pluck('magento_url')->first();
            if ($websiteUrl != null and $websiteUrl != '') {
                $bits = parse_url($websiteUrl);
                if (isset($bits['host'])) {
                    $web = $bits['host'];
                    if (! Str::contains($websiteUrl, 'www')) {
                        $web = 'www.' . $bits['host'];
                    }
                    $websiteUrl = 'https://' . $web;
                    $conf['data'] = [];
                    foreach ($settings as $setting) {
                        $conf['data'][] = ['path' => $setting['path'], 'scope' => $setting['scope'], 'scope_id' => $setting['scope_id']];
                    }
                    $curl = curl_init();
                    $url = $websiteUrl . '/rest/V1/configvalue/get';
                    // Set cURL options
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 300,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($conf),
                        CURLOPT_HTTPHEADER => [
                            'content-type: application/json',
                        ],
                    ]);

                    // Get response
                    $response = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    LogRequest::log($startTime, $url, 'POST', json_encode($conf), json_decode($response), $httpcode, \App\Http\Controllers\MagentoSettingsController::class, 'handle');

                    $response = json_decode($response, true);

                    foreach ($settings as $key => $setting) {
                        $value_on_magento = isset($response[$key]) ? $response[$key]['value'] : null;
                        MagentoSetting::where('id', $setting['id'])
                                        ->update([
                                            'value_on_magento' => $value_on_magento,
                                        ]);
                    }
                    curl_close($curl);
                }
            }
        }

        $this->info('Command executed successfully!');
    }
}
