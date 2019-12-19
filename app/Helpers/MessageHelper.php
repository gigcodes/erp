<?php

namespace App\Helpers;

use \App\ChatMessage;

class MessageHelper
{
    const TXT_ARTICLES = [
        "a", "an", "the",
    ];

    const TXT_PREPOSITIONS = [
        "aboard", "about", "above", "across", "after", "against", "along", "amid", "among", "anti",
        "around", "as", "at", "before", "behind", "below", "beneath", "beside", "besides",
        "between", "beyond", "but", "by", "concerning", "considering", "despite", "down", "during",
        "except", "excepting", "excluding", "following", "for", "from", "in", "inside", "into",
        "like", "minus", "near", "of", "off", "on", "onto", "opposite", "outside", "over", "past",
        "per", "plus", "regarding", "round", "save", "since", "than", "through", "to", "toward",
        "towards", "under", "underneath", "unlike", "until", "up", "upon", "versus", "via", "with",
        "within", "without",
    ];

    public static function getMostUsedWords()
    {
        $chatMessages = ChatMessage::where("customer_id", ">", 0)
            ->where("message", "!=", "")
            ->whereNotNull("number")
            ->select("message")->groupBy("message")->get()->pluck("message","id");

        //rows here should be replaced by the SQL result
        $wordTotals = [];
        $phraces    = [];
        foreach ($chatMessages as $id => $row) {
            $words = explode(" ", $row);
            foreach ($words as $word) {
                if (!in_array($word, self::TXT_ARTICLES + self::TXT_PREPOSITIONS) && $word != "") {
                    $phraces[$word][] = ["txt"=>$row,"id" => $id];
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
        foreach ($wordTotals as $word => $count) {
            $records['words'][$word] = [
                "word"    => $word,
                "total"   => $count
            ];

            $records['phraces'][$word] = [
                "phraces" => isset($phraces[$word]) ? array_unique($phraces[$word]) : [],
            ];

        }
        return $records;

    }

}
