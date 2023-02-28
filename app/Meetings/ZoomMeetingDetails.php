<?php

namespace App\Meetings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ZoomMeetingDetails extends Model
{
    use HasFactory, SoftDeletes;   
    protected $table='zoom_meeting_recordings'; 
    protected $fillable = ['file_name', 'file_path', 'download_url_id', 'description'];
    
}
