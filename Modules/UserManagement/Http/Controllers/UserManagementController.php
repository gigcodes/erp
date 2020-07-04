<?php

namespace Modules\UserManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "User management";
        return view('usermanagement::index', compact('title'));
    }

    public function records(Request $request)
    {
        $user = \App\User::where("is_active", 1);

        if($request->keyword != null) {
            $user = $user->where(function($q) use ($request) {
                $q->where("email","like","%".$request->keyword."%")
                ->orWhere("name","like","%".$request->keyword."%")
                ->orWhere("phone","like","%".$request->keyword."%");
            });
        }


        $user = $user->select(["users.*"])->paginate(12);

        $limitchacter = 50;

        $items = [];
        if (!$user->isEmpty()) {
            foreach ($user as $u) {
                $currentRate = $u->latestRate;

                $u["hourly_rate"] = ($currentRate) ? $currentRate->hourly_rate : 0;
                $u["currency"]    = ($currentRate) ? $currentRate->currency : "USD";

                // setup task list
                $taskList = $u->taskList;
                if (!$taskList->isEmpty()) {
                    $u["current_task_desc"] = isset($taskList[0]) ? $taskList[0]->detail()->task_id . "-" . $taskList[0]->detail()->task : "";
                    $u["next_task_desc"]    = isset($taskList[1]) ? $taskList[1]->detail()->task_id . "-" . $taskList[1]->detail()->task : "";
                    $u["current_task"]      = (strlen($u["current_task_desc"]) > $limitchacter) ? substr($u["current_task_desc"], 0, $limitchacter)."..." : $u["current_task_desc"];
                    $u["next_task"]         = (strlen($u["next_task"]) > $limitchacter) ? substr($u["next_task"], 0, $limitchacter)."..." : $u["next_task"];
                }

                $u["yesterday_hrs"] = $u->yesterdayHrs();

                $items[] = $u;
            }
        }

        return response()->json([
            "code"       => 200,
            "data"       => $items,
            "pagination" => (string) $user->links(),
            "total"      => $user->total(),
            "page"       => $user->currentPage(),
        ]);

    }
}
