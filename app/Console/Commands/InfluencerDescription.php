<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\ScrapInfluencer;
use App\Helpers\LogHelper;
use Illuminate\Console\Command;

class InfluencerDescription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'influencer:description';

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
        LogHelper::createCustomLogForCron($this->signature, ['message' => 'cron was started.']);
        try {
            $report = \App\CronJobReport::create([
                'signature'  => $this->signature,
                'start_time' => Carbon::now(),
            ]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report was added.']);
            $influencers = ScrapInfluencer::all();
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'Scrap influencer query finished.']);
            foreach ($influencers as $influencer) {
                //Getting the email
                if (strpos($influencer->description, '.com') !== false) {
                    preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $influencer->description, $matches);
                    if (isset($matches[0]) && ! empty($matches[0])) {
                        $influencer->email = $matches[0][0];
                        $influencer->save();
                    }
                }

                //Country
                $countries = Config('countries');
                foreach ($countries as $country) {
                    if (strpos(strtolower($influencer->description), strtolower($country['name'])) !== false) {
                        $influencer->country = $country['name'];
                        $influencer->save();
                    }
                }

                //Website
                if (strpos($influencer->description, '.com') !== false) {
                    preg_match_all("#\bhttp?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#", $influencer->description, $matches);
                    if (isset($matches[0])) {
                        if (isset($matches[0][0])) {
                            if (strpos($influencer->description, 'facebook') !== false) {
                                $influencer->facebook = $matches[0][0];
                                $influencer->save();
                            }
                            if (strpos($influencer->description, 'twitter') !== false) {
                                $influencer->twitter = $matches[0][0];
                                $influencer->save();
                            }
                        }
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
            LogHelper::createCustomLogForCron($this->signature, ['message' => 'report endtime was updated.']);
        } catch (\Exception $e) {
            LogHelper::createCustomLogForCron($this->signature, ['Exception' => $e->getTraceAsString(), 'message' => $e->getMessage()]);

            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
