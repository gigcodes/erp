<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserFeedbackCategory;
use App\UserFeedbackStatus;
use App\User;

class UserManagementController extends Controller
{
    public function addFeedbackTableData(Request $request)
    {
        $status = UserFeedbackStatus::get();
        $user_id = '';
        if(\Auth::user()->isAdmin() == true) {
            $category = UserFeedbackCategory::groupBy('category');
        } else {
            $category = UserFeedbackCategory::where('user_id', \Auth::user()->id)->groupBy('category');
        }
            
        //\Auth::user()->isAdmin()
        if($request->user_id){
            //$category = $category->where('user_id', $request->user_id);
            if(\Auth::user()->isAdmin() == true) {
                $user_id = $request->user_id;
            } else {
                $user_id = \Auth::user()->id;
            }
        }
        $users = User::all();
        $category = $category->paginate(25);
        return view('user-management.get-user-feedback-table',compact('category', 'status','user_id', 'users', 'request'));
    }
}
