<?php

namespace App\Jobs;

use App\ApiResponseMessagesTranslation;
use App\Language;
use App\Translations;
use App\GoogleTranslate;
use App\Models\ApiResponseMessagesTranslationLog;
use App\Models\ReplyLog;
use App\TranslateReplies;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTranslateApiResponseMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $record;

    private $user_id;

    public function __construct($record, $user_id)
    {
        $this->record = $record;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $record = $this->record;
            $id = $this->record->id;
            $user_id = $this->user_id;

            $value = $record->value;

            if ($value != '') {
                $LanguageModel = Language::all();

                for ($i = 0; $i < count($LanguageModel); $i++) {
                    $language = $LanguageModel[$i]->locale;

                    // Check translation SEPARATE LINE exists or not
                    $checkTranslationTable = Translations::select('text')
                                                            ->where('from', 'en')
                                                            ->where('to', $language)
                                                            ->where('text_original', $value)
                                                            ->first();

                    if ($checkTranslationTable) {
                        $data = htmlspecialchars_decode($checkTranslationTable->text, ENT_QUOTES);
                    } else {
                        $data = '';
                        $googleTranslate = new GoogleTranslate();
                        $translationString = $googleTranslate->translate($language, $value);
                        if ($translationString != '') {
                            Translations::addTranslation($value, $translationString, 'en', $language);
                            $data = htmlspecialchars_decode($translationString, ENT_QUOTES);
                        }
                    }

                    if ($data != '') {
                        $translateResponseMessage = ApiResponseMessagesTranslation::where('translate_from', 'en')
                                                                    ->where('translate_to', $language)
                                                                    ->where('api_response_message_id', $id)
                                                                    ->first();

                        if (count((array) $translateResponseMessage) == 0) {
                            $translateResponseMessage = new ApiResponseMessagesTranslation();
                            $translateResponseMessage->created_by = $user_id;
                            $translateResponseMessage->created_at = date('Y-m-d H:i:s');
                        } else {
                            $translateResponseMessage->updated_by = $user_id;
                            $translateResponseMessage->updated_at = date('Y-m-d H:i:s');
                        }

                        $translateResponseMessage->api_response_message_id = $id;
                        $translateResponseMessage->translate_from = 'en';
                        $translateResponseMessage->translate_to = $language;
                        $translateResponseMessage->translate_text = $data;
                        $translateResponseMessage->save();

                        $record->is_flagged = 1;
                        $record->is_translate = 1;
                        $record->save();

                        (new ApiResponseMessagesTranslationLog)->addToLog($id, 'System has translate the message to language (' . $language . ')', 'Translate');
                    } else {
                        (new ApiResponseMessagesTranslationLog)->addToLog($id, 'System unable to translate the message', 'Translate');

                        $record->is_flagged = 0;
                        $record->is_translate = 0;
                        $record->save();
                    }
                }
            }
        } catch(\Exception $e) {
            if (! empty($id)) {
                (new ApiResponseMessagesTranslationLog)->addToLog($id, $e->getMessage(), 'Translate');
            }

            \Log::info('Translation error');
            \Log::info($e->getMessage());
        }
    }
}
