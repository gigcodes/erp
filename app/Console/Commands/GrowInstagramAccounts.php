<?php

namespace App\Console\Commands;

use App\Account;
use Carbon\Carbon;
use App\CronJobReport;
use Illuminate\Console\Command;

class GrowInstagramAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:grow-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $accounts = Account::where('is_seeding', 1)->get();

            foreach ($accounts as $account) {
                $username = $account->last_name;
                $password = $account->password;

                try {
                } catch (\Exception $exception) {
                    $this->warn($account->last_name);
                    $this->info($exception->getMessage());

                    continue;
                }

                $this->warn($username);

                $stage = $account->seeding_stage;

                $account->manual_comment = 1;
                $account->save();

                if ($stage >= 7) {
                    $account->bulk_comment = 1;
                    $account->manual_comment = 0;
                    $account->is_seeding = 0;
                    $account->save();

                    continue;
                }

                $imageSet = [
                    0 => ['1', '2'],
                    1 => ['3', '4'],
                    2 => ['5', '6'],
                    3 => ['7', '8'],
                    4 => ['9', '10'],
                    5 => ['11', '12'],
                    6 => ['13', '14'],
                ];

                $followSet = [
                    0 => ['gucci', 'prada'],
                    1 => ['givenchyofficial', 'tods'],
                    2 => ['alexandermcqueen', 'burberry'],
                    3 => ['balenciaga', 'bulgariofficial'],
                    4 => ['dolcegabbana', 'bottegaveneta'],
                    5 => ['celine', 'chloe'],
                    6 => ['dior', 'fendi'],
                ];

                $imagesToPost = $imageSet[$stage];
                try {
                } catch (\Exception $exception) {
                    $this->info($exception->getMessage());
                }

                foreach ($imagesToPost as $i) {
                    $filename = __DIR__ . '/images/' . $i . '.jpeg';
                    $source = imagecreatefromjpeg($filename);
                    [$width, $height] = getimagesize($filename);

                    $newwidth = 800;
                    $newheight = 800;

                    $destination = imagecreatetruecolor($newwidth, $newheight);
                    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    imagejpeg($destination, __DIR__ . '/images/' . $i . '.jpeg', 100);

                    try {
                    } catch (\Exception $exception) {
                        $this->info($exception->getMessage());
                    }
                }

                $account->seeding_stage++;
                $account->save();
            }

            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
