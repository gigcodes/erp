<?php

namespace App\Console\Commands;

use App\Social\SocialConfig;
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
        $configs = SocialConfig::where([
            'page_id' => $this->argument('page_id'),
            'platform' => 'facebook',
            'status' => 1,
        ])->get();

        foreach ($configs as $config) {
            $pageInfoParams = [ // endpoint and params for getting page
                'endpoint_path' => $config->page_id . '/conversations',
                'fields' => 'user_id,id,created_time',
                'access_token' => $config->page_token,
                'request_type' => 'GET',
            ];

            $response = getFacebookResults($pageInfoParams);

            if (isset($response['data']['data'])) {
                $conversations = $response['data']['data'];
                foreach ($conversations as $conversation) {
                    $contact = $config->contacts()->updateOrCreate(['conversation_id' => $conversation['id']], [
                        'account_id' => $comment['message'] ?? '',
                        'social_config_id' => $config->id,
                        'platform' => 2,
                    ]);

                    $pageInfoParams = [ // endpoint and params for getting page
                        'endpoint_path' => $conversation['id'] . '/messages',
                        'fields' => '',
                        'access_token' => $config->page_token,
                        'request_type' => 'GET',
                    ];

                    $messages = getFacebookResults($pageInfoParams);

                    foreach ($messages['data']['data'] as $message) {
                        $pageInfoParams = [ // endpoint and params for getting page
                            'endpoint_path' => $message['id'],
                            'fields' => 'message,from,to,attachments,created_time,is_unsupported',
                            'access_token' => $config->page_token,
                            'request_type' => 'GET',
                        ];

                        $response = getFacebookResults($pageInfoParams);

                        $message_summary = $response['data'];

                        $contact->messages()->updateOrCreate(['message_id' => $message['id']], [
                            'from' => $message_summary['from'],
                            'to' => $message_summary['to'],
                            'message' => $message_summary['message'],
                            'reactions' => $message_summary['reactions'] ?? null,
                            'is_unsupported' => $message_summary['is_unsupported'] ?? false,
                            'attachments' => $message_summary['attachments'] ?? null,
                            'created_time' => $message_summary['created_time'],
                        ]);
                    }
                }
            }
        }
    }
}
