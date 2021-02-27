<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LawyerSpeciality extends Model
{
    protected $table = 'lawyer_specialities';
    protected $fillable =['title'];

    public function lawyers()
    {
        return $this->hasMany(Lawyer::class, 'speciality_id');
    }
}
