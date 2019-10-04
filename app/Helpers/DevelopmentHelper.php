<?php

namespace App\Helpers;

use App\DeveloperTask;

class DevelopmentHelper
{
    public static function getDeveloperTasks($developerId, $status='In Progress')
    {
        // Get open tasks for developer
        $developerTasks = DeveloperTask::where('user_id', $developerId)
            ->join('task_types', 'task_types.id', '=', 'developer_tasks.task_type_id')
            ->select('*', 'developer_tasks.id as task_id')
            ->where('parent_id', '=', '0')
            ->where('status', $status)
            ->orderBy('priority', 'ASC')
            ->orderBy('subject', 'ASC')
            ->get();

        // Return developer tasks
        return $developerTasks;
    }
}