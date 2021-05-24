<?php

namespace App\Exports;

use App\Customer;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HubstaffActivityReport implements FromArray, ShouldAutoSize, WithHeadings
{
  protected $user;

  public function __construct(array $user)
  {
    $this->user = $user;

  }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function array(): array
    {
      $new_customers = [];
      // dd( $this->user );
      $totalApproved = 0;
      $totalDiff = 0;
      foreach ($this->user as $mkey => $user) {
      	foreach($user['tasks'] as $key =>  $ut) {
	      	
	      	@list($taskid,$devtask,$taskName,$estimation,$status,$devTaskId) = explode("||",$ut);

	      	$trackedTime = \App\Hubstaff\HubstaffActivity::where('task_id', $taskid)->sum('tracked');
	      	$time_history = \App\DeveloperTaskHistory::where('developer_task_id',$devTaskId)->where('attribute','estimation_minute')->where('is_approved',1)->first();

	      	if($time_history) {
                $est_time = $time_history->new_value;
            }
            else {
                $est_time = 'N/A';
            }

            if (is_numeric($estimation) && $trackedTime && $taskName){
                $diff =  $estimation - number_format($trackedTime / 60,2,".",",");
            }else{
                $diff = 'N/A';
            }

	        $new_customers[$key]['User'] = $user['userName'];
	        $new_customers[$key]['TaskId'] = $devtask;
	        $new_customers[$key]['TimeAppr'] = $est_time;
	        $new_customers[$key]['TimeDiff'] = $diff;

	        if (is_numeric($est_time)) {
	        	$totalApproved += $est_time;
	        }

	        if (is_numeric($diff)) {
	        	$totalDiff += $diff;
	        }
      	}
      }

      array_push($new_customers, ['Total ',null,$totalApproved,$totalDiff]);
      return $new_customers;
    }

    public function headings() : array
    {
        return ["User", "Task", "Time approved", "Time Diff"];
    }
}
