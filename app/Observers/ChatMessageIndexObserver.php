<?php

namespace App\Observers;

use App\ChatMessage;
use App\Elasticsearch\Elasticsearch;
use App\Elasticsearch\Reindex\Interfaces\Reindex;
use App\Elasticsearch\Reindex\Messages;
use App\Models\IndexerState;

class ChatMessageIndexObserver
{
    public function created(ChatMessage $chatMessage): void
    {
        $message = new Messages;
        $message->partialReindex($chatMessage);
    }

    public function updated(ChatMessage $chatMessage): void
    {
        $message = new Messages;
        $message->partialReindex($chatMessage);
    }
}
