<?php

namespace App\Console\Commands;

use App\MagentoCommand;
use App\MagentoCommandRunLog;
use App\MagentoDevScripUpdateLog;
use App\StoreWebsite;
use Illuminate\Console\Command;

class MagentoRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MagentoCreatRunCommand {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Magento Create Command';

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
            $magCom = MagentoCommand::find($this->argument('id'));
            if($magCom->website_ids == 'ERP') {
                $cmd = $magCom->command_type;
                $allOutput = [];
                $allOutput[] = '../'.$cmd;
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
                MagentoCommandRunLog::create(
                    [
                        'command_id' => $magCom->id,
                        'user_id' => \Auth::user()->id ?? '',
                        'website_ids' => 'ERP',
                        'command_name' => $cmd,
                        'server_ip' => '',
                        'command_type' => $magCom->command_type,
                        'response' => $result,
                    ]
                );
            } else {
                $websites = StoreWebsite::whereIn('id', explode(',', $magCom->website_ids))->get();
                foreach ($websites as $website) {
                    if ($magCom->command_name != '' && $website->server_ip != '') {
                        //$cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH').$magCom->command_name.' --server ' . $magCom->server_ip.' --type custom --command ' . $website->command_type;
                        $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-commands.sh  --server ' . $website->server_ip . " --type custom --command '" . $magCom->command_type . "'";
                        if ($magCom->command_name == 'bin/magento cache:f' || $magCom->command_name == "'bin/magento cache:f'") {
                            $cmd = 'bash ' . getenv('DEPLOYMENT_SCRIPTS_PATH') . 'magento-commands.sh  --server ' . $website->server_ip . " --type custom --command 'bin/magento cache:f'";
                        }
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
                        MagentoCommandRunLog::create(
                            [
                                'command_id' => $magCom->id,
                                'user_id' => \Auth::user()->id ?? '',
                                'website_ids' => $website->id,
                                'command_name' => $cmd,
                                'server_ip' => $website->server_ip,
                                'command_type' => $magCom->command_type,
                                'response' => $result,
                            ]
                        );
                    } else {
                        //\DB::enableQueryLog();
                        $add = MagentoCommandRunLog::create(
                            [
                                'command_id' => $magCom->id ?? '',
                                'user_id' => \Auth::user()->id ?? '',
                                'website_ids' => $website->id,
                                'command_name' => $cmd ?? '',
                                'server_ip' => $website->server_ip ?? '',
                                'command_type' => $magCom->command_type ?? '',
                                'response' => 'Server IP and Command not found',
                            ]);
                        //dd(\DB::getQueryLog());
                    }
                } //end website foreach
            }
        } catch (\Exception $e) {
            MagentoDevScripUpdateLog::create(
                [
                    'command_id' => $magCom->id,
                    'user_id' => \Auth::user()->id ?? '',
                    'website_ids' => $magCom->website_ids,
                    'command_name' => $cmd,
                    'server_ip' => '',
                    'command_type' => $magCom->command_type,
                    'response' => ' Error '.$e->getMessage(),
                ]
            );
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
