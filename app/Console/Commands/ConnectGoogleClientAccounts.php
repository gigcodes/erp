<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\GoogleClientAccount;

class ConnectGoogleClientAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ConnectGoogleClientAccounts';

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
		$GoogleClientAccounts = GoogleClientAccount::orderBy("created_at","desc")->get();
        $google_redirect_url = route('googlewebmaster.get-access-token');
        
        foreach($GoogleClientAccounts as $acc){
            // dump($acc);
            $gClient = new \Google_Client();

            $gClient->setApplicationName($acc->GOOGLE_CLIENT_APPLICATION_NAME);

            $gClient->setClientId($acc->GOOGLE_CLIENT_ID);

            $gClient->setClientSecret($acc->GOOGLE_CLIENT_SECRET);

            $gClient->setDeveloperKey($acc->GOOGLE_CLIENT_KEY);

            $gClient->setRedirectUri($google_redirect_url);

            $gClient->setScopes(array(
                'https://www.googleapis.com/auth/webmasters',
            ));  
            // dump($gClient);
            $google_oauthV2 = new \Google_Service_Oauth2($gClient);
            // dump($google_oauthV2);
            // $gClient->authenticate($request->get('code'));
            if($gClient->getAccessToken()){
                dump($gClient->getAccessToken());
            }
            else
            {
                $authUrl = $gClient->createAuthUrl();
                dump($authUrl, 12);
            }
        }





        dd('end');
    }
}
