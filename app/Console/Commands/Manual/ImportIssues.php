<?php

namespace App\Console\Commands\Manual;

use App\DeveloperTask;
use App\Issue;
use Illuminate\Console\Command;
use App\SkuColorReferences;

class ImportIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:issues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console command for  Import issues';

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
        $issues = Issue::all();
        $data   = array();
        foreach ($issues as $issue){
            $data[] = array(
                'user_id'               =>  $issue->user_id,
                'module_id'             =>  $issue->module,
                'priority'              =>  $issue->priority,
                'subject'               =>  $issue->subject,
                'task'                  =>  $issue->issue,
                'status'                =>  'Planned',
                'created_by'            =>  $issue->submitted_by,
                'is_resolved'           =>  $issue->is_resolved,
                'estimate_time'         =>  $issue->estimate_time,
                'cost'                  =>  $issue->cost,
                'task_type_id'          =>  2,
                'responsible_user_id'   =>  $issue->responsible_user_id,
                'created_at'            =>  $issue->created_at
            );
        }

        $developer_task = DeveloperTask::insert($data);

    }
}