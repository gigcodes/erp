<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateMailinglistInfluencers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-mailinglist-influencers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is using for create mailing list from influencers ';

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
        $services = \App\Service::first();
        $service_id=$services->id;
        $influencers = \App\ScrapInfluencer::where('email','!=',"")->get();
        $websites = \App\StoreWebsite::select('id','title')->orderBy('id','desc')->get();
        foreach($influencers as $influencer)
        {
            $email= $influencer->email;
            foreach($websites as $website)
                {
                    $name=$website->name;
                    if ($name!='')
                    $name=$name."_".date("d_m_Y");
                    else
                       $name='WELCOME_LIST_'.date("d_m_Y");
                   
                    if (!\App\Mailinglist::where('email',$email)->where('website_id',$website->id)->first())
                    {
                        
                        $curl = curl_init();
                        $data = [
                            "folderId" => 1,
                            "name" =>$name
                        ];
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://api.sendinblue.com/v3/contacts/lists",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => json_encode($data),
                            CURLOPT_HTTPHEADER => array(
                                // "api-key: ".getenv('SEND_IN_BLUE_API'),
                                "api-key: ".config('env.SEND_IN_BLUE_API'),
                                "Content-Type: application/json"
                            ),
                        ));
        
                        $response = curl_exec($curl);
        
                        curl_close($curl);
                        \Log::info($response);
                        $res = json_decode($response);
                        
        
                        \App\Mailinglist::create([
                            'name' => $name,
                            'website_id' => $website->id,
                            'service_id' => $service_id,
                            'remote_id' => $res->id,
                        ]);
                        
                    }
                    
                }
        } 
           
       
    }
}   