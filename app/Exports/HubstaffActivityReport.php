<?php

namespace App\Exports;

use App\Customer;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class HubstaffActivityReport implements FromArray, ShouldAutoSize, WithHeadings
{
  protected $user;

  public function __construct(array $user)
  {
    $this->user = $user;

  }

  	public function registerEvents(): array
	{
	    return [
            // handle by a closure.
            AfterSheet::class => function(AfterSheet $event) {

                // get layout counts (add 1 to rows for heading row)
                $row_count = $this->results->count() + 1;
                $column_count = count($this->results[0]->toArray());
                dd( $column_count );

                // set columns to autosize
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
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
      $totalTrack = 0;
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
	        $new_customers[$key]['TimeTracked'] =  ( $trackedTime ) ? number_format($trackedTime / 60,2,".",",") : 'N/A';
	        $new_customers[$key]['status'] = $status;
	        

	        if (is_numeric($est_time)) {
	        	$totalApproved += $est_time;
	        }

	        if (is_numeric($diff)) {
	        	$totalDiff += $diff;
	        }
	        if ($trackedTime && $taskName) {
	        	 $totalTrack += $trackedTime;
	        }

      	}
      }

      array_push($new_customers, ['Total ',null,$totalApproved,$totalDiff, number_format($totalTrack / 60,2,".",",")]);
      return $new_customers;
    }

    public function headings() : array
    {
        return ["User", "Task", "Time approved", "Time Diff", "Time tracked", "Status"];
    }
}
