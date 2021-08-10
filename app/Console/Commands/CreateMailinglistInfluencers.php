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
                    $name='WELCOME_LIST_'.date("d_m_Y");
                    if (!\App\Mailinglist::where('email',$email)->where('website_id',$website->id)->first())
                    {
                        $data=[
                            'website_id' => $website->id,
                            'service_id' => $service_id,
                            'email'=>$email,
                            'name'=>$name
                        ];
                        \App\Mailinglist::insert($data);
                        
                    }
                    
                }
        } 
           
        return redirect()->back()->with('message', 'Mailing LIst Added Successfully');
    }
}   