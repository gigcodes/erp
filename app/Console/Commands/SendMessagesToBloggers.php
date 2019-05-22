<?php

namespace App\Console\Commands;

use App\Account;
use App\Influencers;
use Illuminate\Console\Command;
use InstagramAPI\Instagram;

class SendMessagesToBloggers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bloggers:send-message';

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
        $bloggers = Influencers::whereDoesntHave('messages')->get();

        foreach ($bloggers as $blogger) {
            $targetUsername = $blogger->username;
            $account = Account::where('platform', 'instagram')->inRandomOrder()->first();
            $message = '';

            $ig = new Instagram();
            $ig->login($account->last_name, $account->password);
            $userinfo = $ig->people->getInfoByName($targetUsername);

            $ig->direct->sendText([
                'users' => [
                    $userinfo['user']['pk']
                ]
            ], $message);


        }
    }
}
