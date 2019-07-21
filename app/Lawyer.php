<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lawyer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','phone','default_phone','email','address','referenced_by','speciality_id','rating','whatsapp_number','remarks','other'];

    public function lawyerSpeciality()
    {
        return $this->belongsTo(LawyerSpeciality::class,'speciality_id');
    }

    public function getSpecialityAttribute()
    {
        return optional($this->lawyerSpeciality)->title;
    }

    public function chat_message()
    {
        return $this->hasMany(ChatMessage::class,'lawyer_id');
    }
}
