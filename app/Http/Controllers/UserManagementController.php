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

        $user_id = $request->user_id;
        $category = UserFeedbackCategory::groupBy('category');
        if($request->user_id){
            $category = $category->where('user_id', $request->user_id);
        }
        $users = User::all();
        $category = $category->paginate(25);
        return view('user-management.get-user-feedback-table',compact('category', 'status','user_id', 'users'));
    }
}
