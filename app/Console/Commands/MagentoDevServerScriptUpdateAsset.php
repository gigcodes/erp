<?php

namespace App\Console\Commands;

use App\AssetsManager;
use Illuminate\Console\Command;
use App\AssetMagentoDevScripUpdateLog;

class MagentoDevServerScriptUpdateAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoDevUpdateScriptAsset {id?} {folder_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Asset Magento Dev Script Updates';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            $assetmanager = AssetsManager::where('id', $this->argument('id'))->get();

            foreach ($assetmanager as $asset) {
                $folder_name = $this->argument('folder_name');
                if ($folder_name != '' && $asset->ip != '') {
                    $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-dev.sh --server ' . $asset->ip . ' --site ' . $folder_name;
                    $allOutput = [];
                    $allOutput[] = $cmd;
                    $result = exec($cmd, $allOutput);
                    if ($result == '') {
                        $result = 'Not any response';
                    } elseif ($result == 0) {
                        $result = 'Command run success Response ' . $result;
                    } elseif ($result == 1) {
                        $result = 'Command run Fail Response ' . $result;
                    } else {
                        $result = is_array($result) ? json_encode($result, true) : $result;
                    }

                    $create = AssetMagentoDevScripUpdateLog::create(
                        [
                            'asset_manager_id' => $asset->id,
                            'user_id' => \Auth::user()->id,
                            'ip' => $asset->ip,
                            'response' => $result,
                            'command_name' => $cmd,
                            'site_folder' => $folder_name,
                        ]
                    );
                } else {
                    AssetMagentoDevScripUpdateLog::create(
                        [
                            'store_website_id' => $asset->id ?? '',
                            'ip' => $asset->ip ?? '',
                            'user_id' => \Auth::user()->id,
                            'response' => 'Please check Site folder and server ip',
                            'error' => 'Error',
                            'command_name' => 'Not run command. Please server Ip and site folder',
                            'site_folder' => $folder_name ?? '',
                        ]);
                }
            } //end website foreach
        } catch (\Exception $e) {
            AssetMagentoDevScripUpdateLog::create(
                [
                    'store_website_id' => $asset[0]->id ?? '',
                    'user_id' => \Auth::user()->id,
                    'website' => $asset[0]->ip ?? '',
                    'error' => $e->getMessage(),
                    'command_name' => 'Not run command. Please server Ip and site folder',
                    'site_folder' => $folder_name ?? '',
                ]
            );
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
