<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tickets extends Model
{
     protected $table = 'tickets';
     protected $fillable = [
     'customer_id', 'ticket_id', 'subject', 'message', 'assigned_to', 'status_id', 'date','name','email'
     ];



     public function getTicketList($params = array())
     {
          $selectArray[] = $this->table . '.*';
		$query = DB::table($this->table);    
      
          $query->select($selectArray);
		
		$record_per_page = (isset($params['record_per_page']) && $params['record_per_page'] != "" && $params['record_per_page'] > 0) ? $params['record_per_page'] : 10;
		return $query->paginate($record_per_page);
     }
}
