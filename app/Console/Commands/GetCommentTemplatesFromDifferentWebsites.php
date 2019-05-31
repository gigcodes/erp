<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InstagramAPI\Instagram;

class GetCommentTemplatesFromDifferentWebsites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:get-comments-for-auto-reply';

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
        $accounts = [
            'darveys'
        ];

        $instagram = new Instagram();
        $instagram->login('sololuxury.official', 'Insta123!');
        $response = $instagram->request('https://www.instagram.com/darveys/?__a=1')->getDecodedResponse();

        $medias = $response['graphql']['user']['edge_owner_to_timeline_media'];

        dd($medias);
    }
}
