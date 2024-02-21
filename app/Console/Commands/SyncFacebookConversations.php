<?php

namespace App\Console\Commands;

use App\CronJob;
use Carbon\Carbon;
use App\CronJobReport;
use App\Social\SocialConfig;
use App\Services\Facebook\FB;
use Illuminate\Console\Command;

class SyncFacebookConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-dm {page_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all the facebook messages on the page with respect to the page id';

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
     * @return int
     */
    public function handle()
    {
        try {
            $report = CronJobReport::create([
                'signature' => $this->signature,
                'start_time' => Carbon::now(),
            ]);

            $configs = SocialConfig::where([
                'page_id' => $this->argument('page_id'),
                'platform' => 'facebook',
                'status' => 1,
            ])->get();

            foreach ($configs as $config) {
                $fb = new FB($config->page_token);
                $conversations = $fb->getConversations($config->page_id);

                foreach ($conversations['conversations'] as $convo) {
                    $contact = $config->contacts()->updateOrCreate(['conversation_id' => $convo['id']], [
                        'account_id' => $comment['message'] ?? '',
                        'social_config_id' => $config->id,
                        'platform' => 2,
                        'can_reply' => $convo['can_reply'],
                    ]);

                    foreach ($convo['messages'] as $message) {
                        $contact->messages()->updateOrCreate(['message_id' => $message['id']], [
                            'from' => $message['from'],
                            'to' => $message['to'],
                            'message' => $message['message'],
                            'reactions' => $message['reactions'] ?? null,
                            'is_unsupported' => $message['is_unsupported'] ?? false,
                            'attachments' => $message['attachments'] ?? null,
                            'created_time' => $message['created_time'],
                        ]);
                    }
                }
            }
            $report->update(['end_time' => Carbon::now()]);
        } catch (\Exception $e) {
            CronJob::insertLastError($this->signature, $e->getMessage());
        }

        return 0;
    }
}
