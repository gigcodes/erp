<?php

namespace App\Console\Commands;

use App\GoogleClientAccount;
use App\GoogleClientAccountMail;
use App\GoogleClientNotification;
use App\User;
use Illuminate\Console\Command;

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
        $GoogleClientAccountsMails = GoogleClientAccountMail::orderBy('created_at', 'desc')->get();
        $google_redirect_url = route('googlewebmaster.get-access-token');
        $users = User::get();
        $admins = [];
        foreach ($users as $user) {
            if ($user->isAdmin()) {
                $data['id'] = $user->id;
                $data['name'] = $user->name;
                $data['email'] = $user->email;
                $data['phone'] = $user->phone;
                $data['whatsapp_number'] = $user->whatsapp_number;
                $admins[] = $data;
            }
        }

        foreach ($GoogleClientAccountsMails as $acc) {
            $GoogleClientAccount = GoogleClientAccount::find($acc->google_client_account_id);

            $gClient = new \Google_Client();
            $gClient->setClientId($GoogleClientAccount->GOOGLE_CLIENT_ID);
            $gClient->setClientSecret($GoogleClientAccount->GOOGLE_CLIENT_SECRET);
            $gClient->refreshToken($acc->GOOGLE_CLIENT_REFRESH_TOKEN);
            $token = $gClient->getAccessToken();
            if ($token) {
                $acc->GOOGLE_CLIENT_ACCESS_TOKEN = $token['access_token'];
                if ($token['refresh_token']) {
                    $acc->GOOGLE_CLIENT_REFRESH_TOKEN = $token['refresh_token'];
                }
                $acc->expires_in = $token['expires_in'];
                $acc->save();
                dump('acc_id : ' . $acc->id . ' | acc_name : ' . $acc->google_account . ' | is connected.');
            } else {
                foreach ($admins as $admin) {
                    $msg = 'Google Webmaster:: Your account   has been disconnected. <a href="' . route('googlewebmaster.account.connect', $acc->google_client_account_id) . '">Click here</a> to connect';

                    GoogleClientNotification::create([
                        'google_client_id' => $acc->id,
                        'receiver_id' => $admin['id'],
                        'message' => $msg,
                        'notification_type' => 'error',
                    ]);

                    $msg = 'Google Webmaster:: Your account   has been disconnected. Gogo this link to connect: ' . route('googlewebmaster.account.connect', $acc->google_client_account_id);

                    app(\App\Http\Controllers\WhatsAppController::class)->sendWithThirdApi($admin['phone'], $admin['whatsapp_number'], $msg); // for whatsapp

                    dump($acc->id . ' email sent to ' . $admin['name']);
                }
                dump('acc_id : ' . $acc->id . ' | acc_name : ' . $acc->google_account . ' | is not connected.');
            }
        }
        dump('ConnectGoogleClientAccounts command ended.');
    }
}
