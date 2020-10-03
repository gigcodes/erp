<?php

namespace App\Jobs;

use App\ChatbotQuestion;
use App\WatsonAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\Watson\Language\Assistant\V2\AssistantService;
use App\Library\Watson\Language\Workspaces\V1\DialogService;
use App\Library\Watson\Language\Workspaces\V1\EntitiesService;
use App\Library\Watson\Language\Workspaces\V1\LogService;
use App\Library\Watson\Language\Workspaces\V1\IntentService;

class ManageWatson implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $question;
    protected $method;
    protected $storeParams;
    protected $type;
    protected $old_example;
    protected $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($service, $question, array $storeParams, $method, $type = 'value', $old_example = false)
    {
        $this->question = $question;
        $this->method = $method;
        $this->storeParams = $storeParams;
        $this->type = $type;
        $this->old_example = $old_example;
        $this->service = $service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $all_watson_accounts = WatsonAccount::get();

        if ($this->type) {
            $value = $this->question->{$this->type};
        }

        $serviceClass = 'IntentService';

        if ($this->service === 'dialog') {
            $serviceClass = 'DialogService';
        } elseif ($this->service === 'entity') {
            $serviceClass = 'EntitiesService';
        }

        foreach ($all_watson_accounts as $account) {
//            dump($account);


            if ($this->service === 'dialog') {
                $watson = new DialogService(
                    "apiKey",
                    $account->api_key
                );
            } else if ($this->service === 'entity') {
                $watson = new EntitiesService(
                    "apiKey",
                    $account->api_key
                );
            }else{
                $watson = new IntentService(
                    "apiKey",
                    $account->api_key
                );
            }

            $watson->set_url($account->url);

            if ($this->method === 'create') {
                $watson->create($account->work_space_id, $this->storeParams);
            } else if ($this->method === 'update') {
                $watson->update($account->work_space_id, $value, $this->storeParams);
            } else if ($this->method === 'delete') {
                $watson->delete($account->work_space_id, $value);
            } else if ($this->method === 'update_example') {
                $watson->updateExample($account->work_space_id, $value, $this->old_example, $this->storeParams);
            }

        }

    }

    public function fail($exception = null)
    {
        /* Remove data when job fail while creating..... */
        if ($this->method === 'create' && is_object($this->question)) {
            $this->question->delete();
        }
    }
}
