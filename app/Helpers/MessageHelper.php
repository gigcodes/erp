<?php

namespace App\Helpers;

use \App\ChatMessage;

class MessageHelper
{
    CONST TXT_ARTICLES = [
        "a","an","the"
    ];

    CONST TXT_PREPOSITIONS = [
        "aboard", "about", "above", "across", "after", "against", "along", "amid", "among", "anti", 
        "around", "as", "at", "before", "behind", "below", "beneath", "beside", "besides", 
        "between", "beyond", "but", "by", "concerning", "considering", "despite", "down", "during", 
        "except", "excepting", "excluding", "following", "for", "from", "in", "inside", "into", 
        "like", "minus", "near", "of", "off", "on", "onto", "opposite", "outside", "over", "past", 
        "per", "plus", "regarding", "round", "save", "since", "than", "through", "to", "toward", 
        "towards", "under", "underneath", "unlike", "until", "up", "upon", "versus", "via", "with", 
        "within", "without"
    ];


    public static function getMostUsedWords()
    {
        $chatMessages = ChatMessage::where("customer_id" , ">" , 0)->where("message","!=", "")
        ->whereNotNull("number")->select("message")->groupBy("message")->get()->pluck("message");

        //rows here should be replaced by the SQL result
        $wordTotals = [];
        foreach ($chatMessages as $row) {
           $words = explode(" ", $row);
            foreach ($words as $word) {
                if(!in_array($word, self::TXT_ARTICLES + self::TXT_PREPOSITIONS) && $word != "") {
                    if (isset($wordTotals[$word])) {
                        $wordTotals[$word]++; 
                        continue;
                    }

                    $wordTotals[$word] = 1;
                }
            }
        }

        arsort($wordTotals);

        $records = [];
        foreach($wordTotals as $word => $count) {
            $records[] = [
                "word" => $word,
                "total" => $count
            ];
        }
        return $records;

    }
    
}