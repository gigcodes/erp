<?php

namespace App\Http\Controllers\Github;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    //
    public function listGroups(){
        return view('github.groups');
    }
}
