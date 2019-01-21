<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instruction;
use App\Setting;
use App\Helpers;
use App\User;
use App\NotificationQueue;
use App\PushNotification;
use Carbon\Carbon;
use Auth;

class InstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM')) {
        if ($request->user[0] != null) {
          $instructions = Instruction::whereNull('completed_at')->whereIn('assigned_to', $request->user)->latest()->paginate(Setting::get('pagination'));
          $completed_instructions = Instruction::whereNotNull('completed_at')->whereIn('assigned_to', $request->user)->latest()->paginate(Setting::get('pagination'), ['*'], 'completed-page');
        } else {
          $instructions = Instruction::whereNull('completed_at')->latest()->paginate(Setting::get('pagination'));
          $completed_instructions = Instruction::whereNotNull('completed_at')->latest()->paginate(Setting::get('pagination'), ['*'], 'completed-page');
        }
      } else {
        $instructions = Instruction::whereNull('completed_at')->where('assigned_to', Auth::id())->latest()->paginate(Setting::get('pagination'));
        $completed_instructions = Instruction::whereNotNull('completed_at')->where('assigned_to', Auth::id())->latest()->paginate(Setting::get('pagination'), ['*'], 'completed-page');
      }

      $users_array = Helpers::getUserArray(User::all());
      $user = $request->user ? $request->user : [];

      return view('instructions.index')->with([
        'instructions'            => $instructions,
        'completed_instructions'  => $completed_instructions,
        'users_array'             => $users_array,
        'user'                    => $user
      ]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'instruction' => 'required|min:3',
        'customer_id' => 'required|numeric',
        'assigned_to' => 'required|numeric'
      ]);

      $instruction = new Instruction;
      $instruction->instruction = $request->instruction;
      $instruction->customer_id = $request->customer_id;
      $instruction->assigned_from = Auth::id();
      $instruction->assigned_to = $request->assigned_to;

      $instruction->save();

      NotificationQueueController::createNewNotification([
        'message' => 'Reminder for Instructions',
        'timestamps' => ['+10 minutes'],
        'model_type' => Instruction::class,
        'model_id' =>  $instruction->id,
        'user_id' => Auth::id(),
        'sent_to' => $instruction->assigned_to,
        'role' => '',
      ]);

      return back()->with('success', 'You have successfully created instruction!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function complete(Request $request)
    {
      $instruction = Instruction::find($request->id);
      $instruction->completed_at = Carbon::now();
      $instruction->save();

      NotificationQueue::where('model_type', 'App\Instruction')->where('model_id', $instruction->id)->delete();
      PushNotification::where('model_type', 'App\Instruction')->where('model_id', $instruction->id)->delete();

      $url = route('customer.show', $instruction->customer->id) . '#internal-message-body';

      return response()->json(['instruction'  => $instruction->instruction, 'time' => "$instruction->completed_at", 'url'  => "$url"]);
    }

    public function pending(Request $request)
    {
      $instruction = Instruction::find($request->id);
      $instruction->pending = 1;
      $instruction->save();

      return response("success");
    }

    public function completeAlert(Request $request)
    {
      $instruction = Instruction::find($request->id);

      PushNotification::where('model_type', 'App\Instruction')->where('model_id', $request->id)->delete();

      NotificationQueueController::createNewNotification([
        'message' => 'Reminder for Instructions',
        'timestamps' => ['+10 minutes'],
        'model_type' => Instruction::class,
        'model_id' =>  $instruction->id,
        'user_id' => Auth::id(),
        'sent_to' => $instruction->assigned_to,
        'role' => '',
      ]);

      return redirect()->route('instruction.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
