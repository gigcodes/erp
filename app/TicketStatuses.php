<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketStatuses extends Model
{
     protected $table = 'ticket_statuses';
     protected $fillable = ['name'];
}
