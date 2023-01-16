<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestCaseUserHistory extends Model
{
    protected $table = 'test_case_user_histories';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];
}
