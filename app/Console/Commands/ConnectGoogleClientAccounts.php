<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\GoogleClientAccount;
use App\GoogleClientNotification;
use App\User;
use DB;

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
        $users = User::get();
        $admins = [];
        foreach($users as $user){
            if($user->isAdmin()){
                $data['id'] = $user->id;
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['phone'] = $user->phone;
                $data['whatsapp_number'] = $user->whatsapp_number;
                $admins[] = $data;
            }
        }
        dump(['admins' => $admins]);
        foreach($GoogleClientAccounts as $acc){
            if($acc->GOOGLE_CLIENT_REFRESH_TOKEN){
                try{
                    $gClient = new \Google_Client();
                    $gClient->setClientId($acc->GOOGLE_CLIENT_ID);
                    $gClient->setClientSecret($acc->GOOGLE_CLIENT_SECRET);
                    $gClient->refreshToken($acc->GOOGLE_CLIENT_REFRESH_TOKEN);
                    $token = $gClient->getAccessToken();
                    $acc->GOOGLE_CLIENT_ACCESS_TOKEN = $token['access_token'];
                    $acc->GOOGLE_CLIENT_REFRESH_TOKEN = $token['refresh_token'];
                    $acc->expires_in = $token['expires_in'];
                    $acc->is_active = 1;
                    $acc->save();
                    dump($acc->id . ' accept_token saved.');
                }catch(\Exception $e){
                    foreach($admins as $admin){
                        // $msg = 'please connect this client id ' . $acc->GOOGLE_CLIENT_ID; // for whatsapp
                        Mail::send('google_client_accounts.index', ['admin' => $admin, 'google_redirect_url' => $google_redirect_url, 'acc' => $acc], function($message)use($admin) {
                            $message->to($admin['email'])
                                    ->subject('Connect client ID');  
                        });
                        $html = view('google_client_accounts.index', ['admin' => $admin, 'acc' => $acc]);
                        GoogleClientNotification::create([
                            'google_client_id' => $acc->id,
                            'receiver_id' => $admin['id'],
                            'message' => 'refresh token is invalid',
                            'notification_type' => 'error' 
                        ]);
                        // app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($admin['phone'], $admin['whatsapp_number'], $msg); // for whatsapp
                        dump($acc->id . ' email sent to ' . $admin['name']);
                    }
                    dump($acc->id . ' refresh token is invalid.');
                }
            } 
        }
        dump('ConnectGoogleClientAccounts command ended.');
    }
}
