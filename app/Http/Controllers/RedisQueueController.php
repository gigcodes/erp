<?php

namespace App\Http\Controllers;

use App\RedisQueue;
use App\RedisQueueCommandExecutionLog;
use App\Setting;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RedisQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $queues = RedisQueue::paginate(Setting::get('pagination'));
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('redis_queue.data', compact('queues'))->render(),
                'links' => (string) $queues->render(),
            ], 200);
        }

        return view('redis_queue.index', compact('queues'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $queue = new RedisQueue();
        $queue->user_id = Auth::user()->id ?? '';
        $queue->name = $request->name;
        $queue->type = $request->type;
        $response = $queue->save();
        if ($response) {
            return redirect()->back()->with('success', 'Queue has been created!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RedisQueue  $redisQueue
     * @return \Illuminate\Http\Response
     */
    public function edit(RedisQueue $redisQueue)
    {
        try {
            $id = request('id') ?? '';
            $queue = RedisQueue::query();
            if ($s = $id) {
                $queue->where('id', $s);
            }
            $queues = $queue->orderBy('id', 'desc')->first();

            return response()->json(['code' => 200, 'data' => $queues, 'message' => 'Your Todo List has been listed!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $queue = RedisQueue::findorfail($request->id);
            $queue->user_id = Auth::user()->id ?? '';
            $queue->name = $request->name;
            $queue->type = $request->type;
            $queue->save();

            return redirect()->back()->with('success', 'Queue has been Updated!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $queue = RedisQueue::find($request->id);
        $response = $queue->delete();
        if ($response) {
            return response()->json(['code' => 200, 'message' => 'Queue has been deleted successfully!']);
        } else {
            return response()->json(['code' => 500, 'message' => 'Something went wrong']);
        }
    }

    /**
     * Execute queue.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function execute(Request $request)
    {
        $command = $request->get('command_tail');
        if ($command == 'start') {
            $keyword = 'work';
        } elseif ($command == 'restart') {
            $keyword = 'restart';
        }

        $queue = RedisQueue::find($request->get('id'));
        $cmd = 'queue:'.$keyword.' redis --queue="'.$queue->name.'" --sleep=3 --tries=3';
        try {
            $response = Artisan::call($cmd);

            $command = new RedisQueueCommandExecutionLog();
            $command->user_id = \Auth::user()->id;
            $command->redis_queue_id = $queue->id;
            $command->command = $cmd;
            $command->server_ip = env('SERVER_IP');
            $command->response = $response;
            $command->save();

            return response()->json(['code' => 200, 'message' => 'Queue command executed successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $command = new RedisQueueCommandExecutionLog();
            $command->user_id = \Auth::user()->id;
            $command->redis_queue_id = $queue->id;
            $command->command = $cmd;
            $command->server_ip = env('SERVER_IP');
            $command->response = $msg;
            $command->save();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Execute queue.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function executeHorizon(Request $request)
    {
        $cmd = $request->get('command_tail');
        try {
            $response = Artisan::call($cmd);

            $command = new RedisQueueCommandExecutionLog();
            $command->user_id = \Auth::user()->id;
            $command->command = $cmd;
            $command->server_ip = env('SERVER_IP');
            $command->response = $response;
            $command->save();

            return response()->json(['code' => 200, 'message' => 'Queue command executed successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $command = new RedisQueueCommandExecutionLog();
            $command->user_id = \Auth::user()->id;
            $command->command = $cmd;
            $command->server_ip = env('SERVER_IP');
            $command->response = $msg;
            $command->save();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Get queue command execution log.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function commandLogs($id)
    {
        $logs = RedisQueueCommandExecutionLog::with('user')
            ->with('queue')->where('redis_queue_id', $id)
            ->orderBy('id', 'desc')->get();

        return response()->json(['code' => 200, 'data' => $logs]);
    }
}