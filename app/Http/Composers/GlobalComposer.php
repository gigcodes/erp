<?php


namespace App\Http\Composers;
use App\Helpers\PermissionCheck;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Request;
use Route;
use App\UserLog;


class GlobalComposer
{
    public function compose(View $view)
    {

        if(auth()->check() == true){
           $currentPath= Route::getFacadeRoot()->current()->uri();
            $permission = new PermissionCheck();
            $per = $permission->checkUser($currentPath);
            if($per == true){
                $view->with('currentUser', Auth::user());
            }else{
                header("Location: \unauthorized");
                die();
            }
        }else{
            $view->with('currentUser', Auth::user());
        }
    }
}