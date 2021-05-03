<?php


namespace App\Http\Composers;

use App\Helpers\PermissionCheck;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Request;
use Route;
use App\UserLog;
use App\Permission;
use App\PermissionRequest;


class GlobalComposer
{
    public function compose(View $view)
    {
        if (auth()->check() == true) {
            $currentPath = Route::getFacadeRoot()->current()->uri();
            $permission = new PermissionCheck();
            $per = $permission->checkUser($currentPath);
            if ($per == true) {
                $view->with('currentUser', Auth::user());
            } else {
                $url = explode('/', $currentPath);
                $model = $url[0];
                $actions = end($url);
                if ($model != '') {
                    if ($model == $actions) {
                        $genUrl = $model . '-list';
                    } else {
                        $genUrl = $model . '-' . $actions;
                    }
                }
                if (!isset($genUrl)) {
                    $genUrl = '';
                }
                $permission = Permission::where('route', $genUrl)->first();
                if(isset($permission->route)){
                    PermissionRequest::updateOrcreate([
                        'user_id'           => Auth::user()->id,
                        'permission_id'     => $permission->id,
                    ],
                    [
                        'user_id'           => Auth::user()->id,
                        'permission_id'     => $permission->id,
                        'request_date'      => date('Y-m-d H:i:s'),
                        'permission_name'   => $permission->route,
                    ]);
                    echo 'unauthorized permission name '.$permission->route;
                    die();
                }
            }
        } else {
            $view->with('currentUser', Auth::user());
        }
    }
}
