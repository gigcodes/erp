<?php

namespace App\Jobs;

use App\ChatbotQuestion;
use App\Library\Google\DialogFlow\DialogFlowService;
use App\Models\GoogleDialogAccount;
use App\Models\GoogleResponseId;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManageGoogle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $questionId;

    protected $storeParams;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($questionId, array $storeParams)
    {
        $this->questionId = $questionId;
        $this->storeParams = $storeParams;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $google_accounts = GoogleDialogAccount::all();
        $chatBotQuestion = ChatbotQuestion::where('id', $this->questionId)->first();
        if ($chatBotQuestion) {
            $questionArr = [];
            $replyArr = [];
            foreach ($chatBotQuestion->chatbotQuestionExamples as $question) {
                $questionArr[] = $question->question;
            }
            $replyArr = explode(',', $chatBotQuestion->suggested_reply);
            foreach ($chatBotQuestion->chatbotQuestionReplies as $reply) {
                $replyArr[] = $reply->suggested_reply;
            }
            foreach ($google_accounts as $google_account) {
                $dialogService = new DialogFlowService($google_account);
                $response = $dialogService->createIntent([
                    'questions' => $questionArr,
                    'reply' => $replyArr,
                    'name' => $chatBotQuestion['value'],
                    'parent' => $chatBotQuestion['parent']]);
                if ($response) {
                    $name = explode('/', $response);
                    $store_response = new GoogleResponseId();
                    $store_response->google_response_id = $name[count($name) - 1];
                    $store_response->google_dialog_account_id = $google_account->id;
                    $store_response->chatbot_question_id = $chatBotQuestion->id;
                    $store_response->save();
                }
            }
        }
    }
}
