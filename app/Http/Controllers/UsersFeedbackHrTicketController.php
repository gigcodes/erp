<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UsersFeedbackHrTicket;

class UsersFeedbackHrTicketController extends Controller
{
    public function __construct()
    {
        //dd('gfhgfhgf');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $task = new UsersFeedbackHrTicket();
            $task->user_id = \Auth::user()->id ?? '';
            $task->feedback_cat_id = $request->feedback_cat_id;
            $task->task_subject = $request->task_subject;
            $task->task_type = $request->task_type;
            $task->repository_id = $request->repository_id;
            $task->task_detail = $request->task_detail;
            $task->cost = $request->cost;
            $task->task_asssigned_to = $request->task_asssigned_to;
            $task->status = 'In progress';
            $task->save();

            return response()->json(['code' => '200', 'data' => $task, 'message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500',  'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UsersFeedbackHrTicket  $usersFeedbackHrTicket
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $task = UsersFeedbackHrTicket::select('users_feedback_hr_tickets.*', 'users_feedback_hr_tickets.task_subject as subject', 'users.name as assigned_to_name')
            ->join('users', 'users.id', 'users_feedback_hr_tickets.task_asssigned_to')
            ->where('feedback_cat_id', $request->id)->get();

            return response()->json(['code' => '200', 'data' => $task, 'message' => 'Ticket Details listed successfully']);
        } catch (\Exception $e) {
            return response()->json(['code' => '500',  'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(UsersFeedbackHrTicket $usersFeedbackHrTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UsersFeedbackHrTicket $usersFeedbackHrTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(UsersFeedbackHrTicket $usersFeedbackHrTicket)
    {
        //
    }
}
