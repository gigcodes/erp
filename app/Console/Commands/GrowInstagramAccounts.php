<?php

namespace App\Console\Commands;

use App\Account;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;

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
        $accounts = Account::where('is_seeding', 1)->get();

        foreach ($accounts as $account) {
            $username = $account->first_name;
            $password = $account->password;


            $instagram = new Instagram();
            $instagram->login($username, $password);

            $stage = $account->seeding_stage;

            if ($stage >= 10) {
                $account->bulk_comment = 1;
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
                7 => ['15', '16'],
                8 => ['17', '18'],
                9 => ['19', '20'],
            ];

            $followSet = [
                0 => ['gucci', 'prada'],
                1 => ['givenchyofficial', 'tods'],
                2 => ['alexandermcqueen', 'burberry'],
                3 => ['balenciaga', 'bulgariofficial'],
                4 => ['dolcegabbana', 'bottegaveneta'],
                5 => ['celine', 'chloe'],
                6 => ['dior', 'fendi'],
                7 => ['isseymiyakeparfums', 'jimmychoo'],
                8 => ['ysl', 'tomford'],
                9 => ['toryburch', 'stellamccartney'],
            ];

            $imagesToPost = $imageSet[$stage];

            $instagram->people->follow($followSet[$stage][0]);
            $instagram->people->follow($followSet[$stage][1]);

            foreach ($imagesToPost as $i) {
                $filename = __DIR__ . '/images/'. $i . '.jpeg';
                $source = imagecreatefromjpeg($filename);
                list($width, $height) = getimagesize($filename);

                $newwidth = 800;
                $newheight = 800;

                $destination = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagejpeg($destination, __DIR__ . '/images/'. $i . '.jpeg', 100);

                $instagram->timeline->uploadPhoto($filename);

            }

            ++$account->seeding_stage;
            $account->save();

        }

    }
}
