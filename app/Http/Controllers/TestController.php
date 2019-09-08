<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PermissionCheck;
use Route;

class TestController extends Controller
{
    public function index(){
    	 $p = PermissionCheck::checkUser('document');
        if($p == false){
            return view('errors.401');
        }
        $currentPath= Route::getFacadeRoot()->current()->uri();
       // $per = PermissionCheck::checkUser($currentPath);
        $cur = explode("/",$currentPath);
        $model = $cur[0];
        $last = end($cur);
        $route = $model.'-'.$last; 
    	return $route;
    }
}
