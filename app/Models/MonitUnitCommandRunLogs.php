<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitUnitCommandRunLogs extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'xmlid', 'request_data', 'response_data'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->select('name', 'id');
    }
}
