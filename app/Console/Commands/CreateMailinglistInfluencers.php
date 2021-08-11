<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Customer;

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
        $influencers = \App\ScrapInfluencer::where('read_status','!=',1)->where('email','!=',"")->get();
        $websites = \App\StoreWebsite::select('id','title')->orderBy('id','desc')->get();
       
        $email_list=array();
        $email_list2=array();
        $listIds=array();
        foreach($influencers as $influencer)
        {
            $email_list[]=['email'=>$influencer->email,'name'=>$influencer->name,'platform'=>$influencer->platform];
        }

        foreach($websites as $website)
                {
                    $name=$website->title;
                    if ($name!='')
                    $name=$name."_".date("d_m_Y");
                    else
                       $name='WELCOME_LIST_'.date("d_m_Y");
                    $res='';
                    
                    
                    for($count=0;$count<count($email_list);$count++) 
                    {
                        $email=$email_list[$count]['email'];
                        if (!\App\Mailinglist::where('email',$email)->where('website_id',$website->id)->first())
                        {
                            
                           if (!isset($res->id))
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
                                     "api-key: ".getenv('SEND_IN_BLUE_API'),
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
                                 'email' =>$email,
                                 'remote_id' =>$res->id,
                             ]);
                             $listIds[]=$res->id;
     
                           } 
                            if (isset($res->id))
                           {
                           $email_list2[]=['email'=>$email_list[$count]['email'],'name'=>$email_list[$count]['name']];
                           
                            if (!\App\Customer::where('email',$email)->first())
                            {
                                    $customer = new Customer;
                            
                                    $customer->email            = $email;
                                    $customer->name             = $email_list[$count]['name'];
                                    $customer->store_website_id = $website->id;
                                    $customer->save();
                            }   
                           }
                           
                            
                        }

                    }  
                   
                   
                    
                }

       
                for($count=0;$count<count($email_list2);$count++) 
       {
        $email=$email_list2[$count]['email'];
        $curl = curl_init();
        $data = [
           "email" =>$email,
           "listIds" =>$listIds,
           "attributes"=>['firstname'=>$email_list2[$count]['name']]
       ];
       curl_setopt_array($curl, array(
           CURLOPT_URL => "https://api.sendinblue.com/v3/contacts",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => "",
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "POST",
           CURLOPT_POSTFIELDS => json_encode($data),
           CURLOPT_HTTPHEADER => array(
               "api-key: ".getenv('SEND_IN_BLUE_API'),
               "Content-Type: application/json"
           ),
       ));
       $response = curl_exec($curl);

        curl_close($curl);
        
       }       
       
       \App\ScrapInfluencer::update(['read_status'=>1]);
       
    }
}   