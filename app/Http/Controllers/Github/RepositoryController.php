<?php

namespace App\Http\Controllers\Github;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RepositoryController extends Controller
{
    //
    public function listRepositories(){
        return view('github.repositories');
    }
}
