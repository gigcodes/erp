<?php

namespace App\Http\Controllers;

use App\Customer;
use App\UserActions;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Instruction;
use App\Setting;
use App\Helpers;
use App\User;
use App\ChatMessage;
use App\InstructionCategory;
use App\NotificationQueue;
use App\PushNotification;
use Carbon\Carbon;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class InstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $a = new UserActions();
        $a->action = "List";
        $a->page = "Instructions";
        $a->details = "Opened Instruction Page!";
        $a->save();

        $selected_category = $request->category ?? '';
        $orderby = 'ASC';

        if ($request->orderby != '') {
            $orderby = 'DESC';
        }

        if (Auth::user()->hasRole('Admin')) {
            if ($request->user[ 0 ] != null) {
                $instructions = Instruction::with(['Remarks', 'Customer', 'Category'])->where('verified', 0)->where('pending', 0)->whereNull('completed_at')->whereIn('assigned_to', $request->user);
                // $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at')->whereIn('assigned_to', $request->user)->orderBy('category_id', 'ASC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'pending-page')->groupBy('category_id');
                $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at')->whereIn('assigned_to', $request->user);
                // $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at')->whereIn('assigned_to', $request->user)->orderBy('category_id', 'ASC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'verify-page')->groupBy('category_id');
                $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at')->whereIn('assigned_to', $request->user);
                $completed_instructions = Instruction::where('verified', 1)->whereIn('assigned_to', $request->user);

                if ($selected_category != '') {
                    $instructions = $instructions->where('category_id', $selected_category);
                    $pending_instructions = $pending_instructions->where('category_id', $selected_category);
                    $verify_instructions = $verify_instructions->where('category_id', $selected_category);
                    $completed_instructions = $completed_instructions->where('category_id', $selected_category);
                }
                // $completed_instructions = Instruction::where('verified', 1)->whereIn('assigned_to', $request->user)->orderBy('category_id', 'ASC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'completed-page')->groupBy('category_id');

                // dd($instructions);

                // $instruction_categories = InstructionCategory::with('instructions')->whereHas('instructions', function ($query) use ($request, $orderby) {
                //   $query->where('verified', 0)->where('pending', 0)->whereNull('completed_at')->whereIn('assigned_to', $request->user)->orderBy('is_priority', 'DESC')->orderBy('created_at', $orderby);
                // })->get();

                // dd($instructions);
            } else {
                $instructions = Instruction::with(['Remarks', 'Customer', 'Category'])->where('verified', 0)->where('pending', 0)->whereNull('completed_at');
                // $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at')->orderBy('category_id', 'ASC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'pending-page')->groupBy('category_id');
                $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at');
                // $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at')->orderBy('category_id', 'ASC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'verify-page')->groupBy('category_id');
                $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at');
                // $completed_instructions = Instruction::where('verified', 1)->orderBy('category_id', 'ASC')->orderBy('completed_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'completed-page')->groupBy('category_id');
                $completed_instructions = Instruction::where('verified', 1);

                if ($selected_category != '') {
                    $instructions = $instructions->where('category_id', $selected_category);
                    $pending_instructions = $pending_instructions->where('category_id', $selected_category);
                    $verify_instructions = $verify_instructions->where('category_id', $selected_category);
                    $completed_instructions = $completed_instructions->where('category_id', $selected_category);
                }
            }
        } else {
            $instructions = Instruction::with(['Remarks', 'Customer', 'Category'])->where('verified', 0)->where('pending', 0)->whereNull('completed_at')->where('assigned_to', Auth::id());
            // $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at')->where('assigned_to', Auth::id())->orderBy('category_id', 'ASC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'pending-page')->groupBy('category_id');
            $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at')->where('assigned_to', Auth::id());
            // $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at')->where('assigned_to', Auth::id())->orderBy('category_id', 'ASC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'verify-page')->groupBy('category_id');
            $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at')->where('assigned_to', Auth::id());
            // $completed_instructions = Instruction::where('verified', 1)->where('assigned_to', Auth::id())->orderBy('category_id', 'ASC')->orderBy('completed_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'completed-page')->groupBy('category_id');
            $completed_instructions = Instruction::where('verified', 1)->where('assigned_to', Auth::id());

            if ($selected_category != '') {
                $instructions = $instructions->where('category_id', $selected_category);
                $pending_instructions = $pending_instructions->where('category_id', $selected_category);
                $verify_instructions = $verify_instructions->where('category_id', $selected_category);
                $completed_instructions = $completed_instructions->where('category_id', $selected_category);
            }
        }


        $users_array = Helpers::getUserArray(User::all());
        $user = $request->user ? $request->user : [];

        $instructions = $instructions->orderBy('is_priority', 'DESC')->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'));
        $pending_instructions = $pending_instructions->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'pending-page');
        $verify_instructions = $verify_instructions->orderBy('completed_at', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'verify-page');
        $completed_instructions = $completed_instructions->orderBy('completed_at', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'completed-page');

        // if ($request->sortby != 'created_at') {
        //   $instructions = array_values(array_sort($instructions, function ($value) {
        //     if ($value['remarks']) {
        //       return $value['remarks'][0]['created_at'];
        //     }
        //
        //     return NULL;
        // 	}));
        // }
        //
        // $instructions = array_reverse($instructions);
        // dd('at');
        $ids_list = [];
        foreach ($instructions as $data) {
            foreach ($data as $instruction) {
                $ids_list[] = $instruction[ 'customer' ] ? $instruction[ 'customer' ][ 'id' ] : '';
            }
        }

        $categories_array = [];
        $instruction_categories = InstructionCategory::all();

        foreach ($instruction_categories as $category) {
            $categories_array[ $category->id ][ 'name' ] = $category->name;
            $categories_array[ $category->id ][ 'icon' ] = $category->icon;
        }

        // dd($instructions);

        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $perPage = Setting::get('pagination');
        // $currentItems = array_slice($instructions, $perPage * ($currentPage - 1), $perPage);
        //
        // $instructions = new LengthAwarePaginator($currentItems, count($instructions), $perPage, $currentPage, [
        // 	'path'	=> LengthAwarePaginator::resolveCurrentPath()
        // ]);

        return view('instructions.index')->with([
            'instructions' => $instructions,
            'pending_instructions' => $pending_instructions,
            'verify_instructions' => $verify_instructions,
            'completed_instructions' => $completed_instructions,
            'users_array' => $users_array,
            'user' => $user,
            'orderby' => $orderby,
            'categories_array' => $categories_array,
            'customer_ids_list' => json_encode($ids_list),
            'selected_category' => $selected_category
        ]);
    }

    public function list(Request $request)
    {

        $a = new UserActions();
        $a->action = 'List';
        $a->page = 'Instructions';
        $a->details = 'Oened Instructions List Page';
        $a->save();

        $orderby = 'desc';

        if ($request->orderby == '') {
            $orderby = 'asc';
        }

        if ($request->user[ 0 ] != null) {
            $instructions = Instruction::with(['Remarks', 'Customer', 'Category'])->where('verified', 0)->where('pending', 0)->whereNull('completed_at')->whereIn('assigned_to', $request->user)->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->get()->toArray();
            $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at')->whereIn('assigned_to', $request->user)->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'pending-page');
            $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at')->whereIn('assigned_to', $request->user)->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'verify-page');
            $completed_instructions = Instruction::where('verified', 1)->whereIn('assigned_to', $request->user)->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'completed-page');
        } else {
            $instructions = Instruction::with(['Remarks', 'Customer', 'Category'])->where('verified', 0)->where('pending', 0)->whereNull('completed_at')->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->get()->toArray();
            $pending_instructions = Instruction::where('verified', 0)->where('pending', 1)->whereNull('completed_at')->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'pending-page');
            $verify_instructions = Instruction::where('verified', 0)->whereNotNull('completed_at')->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'verify-page');
            $completed_instructions = Instruction::where('verified', 1)->where('assigned_from', Auth::id())->orderBy('created_at', $orderby)->paginate(Setting::get('pagination'), ['*'], 'completed-page');
        }

        $users_array = Helpers::getUserArray(User::all());
        $user = $request->user ? $request->user : [];

        // if ($request->sortby != 'created_at') {
        //   $instructions = array_values(array_sort($instructions, function ($value) {
        //     if ($value['remarks']) {
        //       return $value['remarks'][0]['created_at'];
        //     }
        //
        //     return NULL;
        // 	}));
        // }
        //
        // $instructions = array_reverse($instructions);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        $currentItems = array_slice($instructions, $perPage * ($currentPage - 1), $perPage);

        $instructions = new LengthAwarePaginator($currentItems, count($instructions), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);

        return view('instructions.list')->with([
            'instructions' => $instructions,
            'pending_instructions' => $pending_instructions,
            'verify_instructions' => $verify_instructions,
            'completed_instructions' => $completed_instructions,
            'users_array' => $users_array,
            'user' => $user,
            'orderby' => $orderby
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
     * @param  \Illuminate\Http\Request $request
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
        $instruction->category_id = $request->category_id;
        $instruction->instruction = $request->instruction;
        $instruction->customer_id = $request->customer_id;
        $instruction->assigned_from = Auth::id();
        $instruction->assigned_to = $request->assigned_to;
        $instruction->is_priority = $request->is_priority == 'on' ? 1 : 0;

        $instruction->save();

        // NotificationQueueController::createNewNotification([
        //   'message' => 'Reminder for Instructions',
        //   'timestamps' => ['+10 minutes'],
        //   'model_type' => Instruction::class,
        //   'model_id' =>  $instruction->id,
        //   'user_id' => Auth::id(),
        //   'sent_to' => $instruction->assigned_to,
        //   'role' => '',
        // ]);

        if ($request->send_whatsapp === 'send') {
            $user = User::find($instruction->assigned_to);
            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['remark' => 'Auto message was sent.', 'id' => $instruction->id, 'module_type' => 'instruction']);

            app('App\Http\Controllers\TaskModuleController')->addRemark($myRequest);
            app('App\Http\Controllers\WhatsAppController')->sendWithWhatsApp($user->phone, $user->whatsapp_number, $instruction->instruction);
        }

        $a = new UserActions();
        $a->action = 'List';
        $a->page = 'Instructions';
        $a->details = 'Oened Instructions List Page';
        $a->save();

        if ($request->ajax()) {
            return response('success');
        }

        return back()->with('success', 'You have successfully created instruction!');
    }

    public function categoryStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:255'
        ]);

        $data = $request->except('_token');

        InstructionCategory::create($data);

        return redirect()->back()->with('success', 'You have successfully created instruction category!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);

        Instruction::find($id)->update($data);

        return redirect()->route('instruction.index')->withSuccess('You have successfully updated instruction!');
    }

    public function complete(Request $request)
    {
        $instruction = Instruction::find($request->id);
        $instruction->completed_at = Carbon::now();
        $instruction->save();

        // if ($instruction->instruction == '') {
        //   $message_body = 'Images attached!';
        // } else {
        //   $message_body = 'Instruction Complete!';
        // }

        // ChatMessage::create([
        //   'number'        => NULL,
        //   'customer_id'   => $instruction->customer_id,
        //   'status'        => 4,
        //   'user_id'       => Auth::id(),
        //   'assigned_to'   => $instruction->assigned_to,
        //   'message'       => $instruction->instruction . " - Instruction Completed"
        // ]);

        // $myRequest = new Request();
        // $myRequest->setMethod('POST');
        // $myRequest->request->add([
        //   'moduletype' => (string) 'customer',
        //   'moduleid' => (int) $instruction->customer_id,
        //   'status' => (int) 4,
        //   'userid' => (int) Auth::id(),
        //   'assigned_user' => (int) $instruction->assigned_to,
        //   'body' => $message_body
        // ]);
        //
        // // return response($myRequest);
        //
        // app('App\Http\Controllers\MessageController')->store($myRequest);

        NotificationQueue::where('model_type', 'App\Instruction')->where('model_id', $instruction->id)->delete();
        PushNotification::where('model_type', 'App\Instruction')->where('model_id', $instruction->id)->delete();

        $url = route('customer.show', $instruction->customer->id) . '#internal-message-body';

        Customer::where('id', $instruction->customer->id)->update([
            'instruction_completed_at' => Carbon::now()->toDateTimeString()
        ]);

        return response()->json(['instruction' => $instruction->instruction, 'time' => "$instruction->completed_at", 'url' => "$url"]);
    }

    public function pending(Request $request)
    {
        $instruction = Instruction::find($request->id);
        $instruction->pending = 1;
        $instruction->save();

        return response("success");
    }

    public function verify(Request $request)
    {
        $instruction = Instruction::find($request->id);
        $instruction->verified = 1;
        $instruction->save();

        return response("success");
    }

    public function verifySelected(Request $request)
    {
        $selected_instructions = json_decode($request->selected_instructions);

        foreach ($selected_instructions as $selection) {
            $instruction = Instruction::find($selection);

            if ($instruction[ 'assigned_from' ] == Auth::id() || Auth::user()->hasRole('Admin')) {
                $instruction->verified = 1;
                $instruction->save();
            }
        }

        return redirect()->route('instruction.index')->withSuccess('You have successfully verified instructions');
    }

    public function completeAlert(Request $request)
    {
        $instruction = Instruction::find($request->id);

        PushNotification::where('model_type', 'App\Instruction')->where('model_id', $request->id)->delete();

        // NotificationQueueController::createNewNotification([
        //   'message' => 'Reminder for Instructions',
        //   'timestamps' => ['+10 minutes'],
        //   'model_type' => Instruction::class,
        //   'model_id' =>  $instruction->id,
        //   'user_id' => Auth::id(),
        //   'sent_to' => $instruction->assigned_to,
        //   'role' => '',
        // ]);

        return redirect()->route('instruction.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function quickInstruction(Request $request)
    {
        // Load first open instruction
        $instructions = Instruction::where('verified', 0)->whereNull('completed_at')->whereNotNull('customer_id');

        // Set type
        if ($request->type != null) {
            $instructions = $instructions->where('instruction', 'like', '%' . $request->type . '%');
        }

        // For non-admins
        if (!Auth::user()->hasRole('Admin')) {
            $instructions = $instructions->where('assigned_to', Auth::id());
        }

        // Get the first instruction
        $instruction = $instructions->orderBy('id', 'desc')->first();

        // Return the view with the first instruction
        return view('instructions.quick-instruction')->with([
            'instruction' => $instruction,
            'type' => $request->type ?? ''
        ]);
    }
}
