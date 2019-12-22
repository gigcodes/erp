<?php

namespace App\Console\Commands;

use App\ChatMessagePhrase;
use App\ChatMessageWord;
use Illuminate\Console\Command;

class MostUsedWordsInChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:most-used-words';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message to admin if scraper is not running.';

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
        // start to get the most used words from chat messages
        $mostUsedWords = \App\Helpers\MessageHelper::getMostUsedWords();
        ChatMessagePhrase::truncate();
        ChatMessageWord::truncate();

        if (!empty($mostUsedWords["words"])) {
            ChatMessageWord::insert($mostUsedWords["words"]);
        }

        // start to phrases
        $allwords = ChatMessageWord::all();

        $phrasesRecords = [];
        foreach ($allwords as $words) {
            $phrases = isset($mostUsedWords["phraces"][$words->word]) ? $mostUsedWords["phraces"][$words->word]["phraces"] : [];
            if (!empty($phrases)) {
                foreach ($phrases as $phrase) {
                    /*$phrasesRecords[] = [
                        "word_id" => $words->id,
                        "phrase"  => $phrase["txt"],
                        "chat_id" => $phrase["id"],
                    ];*/
            		ChatMessagePhrase::insert([
                        "word_id" => $words->id,
                        "phrase"  => $phrase["txt"],
                        "chat_id" => $phrase["id"],
                    ]);
                }
            }
        }

        if (!empty($phrasesRecords)) {
        }

    }
}
