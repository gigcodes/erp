<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotQuestionExample extends Model
{
    public $timestamps  = false;
    protected $fillable = [
        'question', 'chatbot_question_id',
    ];

    public function questionModal()
    {
        return $this->hasOne("\App\ChatbotQuestion", "id", "chatbot_question_id");
    }

    public function annotations()
    {
        return $this->hasMany("\App\ChatbotIntentsAnnotation", "question_example_id", "id");
    }

    public function highLightQuestion()
    {
        $getAllLengths = $this->annotations;
        $question = $this->question;
        $selectedAn         = [];
        if (!$getAllLengths->isEmpty()) {
            foreach ($getAllLengths as $lengths) {
                $selectedAn[$lengths->id] = substr($question,$lengths->start_char_range,$lengths->end_char_range);
            }
        }

        return $selectedAn;
    }
}
