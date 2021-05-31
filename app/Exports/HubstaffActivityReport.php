<?php

namespace App\Exports;

use App\Customer;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class HubstaffActivityReport implements FromArray, ShouldAutoSize, WithHeadings, WithEvents
{
  protected $user;

  public function __construct(array $user)
  {
    $this->user = $user;

  }

  	public function registerEvents() : array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // $event->sheet->getDelegate()->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);           
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
            }
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

            if( $devtask ){
    	        $new_customers[$key]['User'] = $user['userName'];
                $new_customers[$key]['date'] = \Carbon\Carbon::parse($user['date'])->format('d-m');
    	        $new_customers[$key]['TaskId'] = $devtask;
    	        $new_customers[$key]['TimeAppr'] = $est_time;
    	        $new_customers[$key]['TimeDiff'] = $diff;
    	        $new_customers[$key]['TimeTracked'] =  ( $trackedTime && $devtask ) ? number_format($trackedTime / 60,2,".",",") : 'N/A';
    	        $new_customers[$key]['status'] = $status;
    	        

    	        if (is_numeric($est_time) && $devtask) {
    	        	$totalApproved += $est_time;
    	        }

    	        if (is_numeric($diff) && $devtask) {
    	        	$totalDiff += $diff;
    	        }
    	        if ($trackedTime && $devtask) {
    	        	 $totalTrack += $trackedTime;
    	        }
            }

      	}
      }

      array_push($new_customers, [null,null,null,null,null, null]);
      array_push($new_customers, ['Total ',null,null,$totalApproved,$totalDiff, number_format($totalTrack / 60,2,".",",")]);
      // dd( $new_customers );
      return $new_customers;
    }

    public function headings() : array
    {
        return ["User", "Date", "Task", "Time approved", "Time Diff", "Time tracked", "Status"];
    }
}
