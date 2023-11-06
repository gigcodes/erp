<?php

namespace App\Observers;

use App\ChatMessage;
use App\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class TaskObserver
{
    public function created(ChatMessage $task)
    {
        $this->clearCache($task);
    }

    public function updated(ChatMessage $task)
    {
        $this->clearCache($task);
    }

    public function deleting(ChatMessage $task)
    {
        $this->clearCache($task);
    }

    private function clearCache($task)
    {
        if ($task->task_id) {
            Cache::flush();
        }
    }
}
