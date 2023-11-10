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
        try {
            $elastic = new Elasticsearch();
            $elastic->connect();
            $elastic->getConn()->ping();
            $message = new Messages;
            $message->indexOne($chatMessage);
        }
        catch (\Exception $e) {
            $indexer = IndexerState::where('index', 'chatbot_messages')->first();
            if ($indexer !== null) {
                $indexer->setStatus(Reindex::INVALIDATE);
            }
        }
    }

    public function updated(ChatMessage $chatMessage): void
    {
        try {
            $elastic = new Elasticsearch();
            $elastic->connect();
            $elastic->getConn()->ping();
            $message = new Messages;
            $message->indexOne($chatMessage);
        }
        catch (\Exception $e) {
            $indexer = IndexerState::find('index', 'chatbot_messages');
            if ($indexer !== null) {
                $indexer->setStatus(Reindex::INVALIDATE);
            }
        }
    }
}
