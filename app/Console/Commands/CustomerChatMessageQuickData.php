<?php

namespace App\Console\Commands;

use App\ChatMessagesQuickData;
use App\Customer;
use Illuminate\Console\Command;

class CustomerChatMessageQuickData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:chat-message-quick-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get customers last message and store it into new table';

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
        try {
            $customers = Customer::with(['allMessages' => function($q) {
                $q->latest()->first();
            }])->get();
    
            if(count($customers)) {
                foreach($customers as $key => $item) {
                    if(count($item->allMessages)) {
                        foreach($item->allMessages as $key1 => $item1) {
                            $data['last_unread_message'] = ($item1->status == 0) ? $item1->message : '';
                            $data['last_unread_message_at'] = ($item1->status == 0) ? $item1->created_at : '';
                            $data['last_communicated_message'] = ($item1->status > 0) ? $item1->message : '';
                            $data['last_communicated_message_at'] = ($item1->status > 0) ? $item1->created_at : '';
                            ChatMessagesQuickData::updateOrCreate([
                                'model' => "\App\Customer",
                                'model_id' => $item->id
                                ], $data);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            \App\CronJob::insertLastError($this->signature, $e->getMessage());
        }
    }
}
