<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'id', 'title','checklist_id','created_at', 'updated_at'
    ];

    public function checklistsubject()
    {
        return $this->hasMany(ChecklistSubject::class)->where('user_id', \Auth::id());
    }
}