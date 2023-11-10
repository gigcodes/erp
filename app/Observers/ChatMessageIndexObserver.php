<?php

namespace App\Observers;

use App\ChatMessage;
use App\Elasticsearch\Reindex\Messages;

class ChatMessageIndexObserver
{
    public function created(ChatMessage $chatMessage): void
    {
        $message = new Messages;

        $message->indexOne($chatMessage);
    }

    public function updated(ChatMessage $chatMessage): void
    {
        $message = new Messages;

        $message->indexOne($chatMessage);
    }
}
