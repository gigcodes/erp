<?php

namespace App\Http\Middleware;

use Closure;
use App\Setting;
use App\LogRequest;

class LogAfterRequest
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $logApis = Setting::where('name', '=', 'log_apis')->first();
        if ($logApis && $logApis->val == 1) {
            $url = $request->fullUrl();
            $ip  = $request->ip();

            $startTime = date('Y-m-d H:i:s', LARAVEL_START);
            $endTime   = date('Y-m-d H:i:s');
            $timeTaken = strtotime($endTime) - strtotime($startTime);
            $route     = app('router')->getRoutes()->match($request);
            $api_name  = '';

            if ($route) {
                $api_name = $route->action['controller'];
                $api_name = explode('@', $api_name);
            }

            $response_array = json_decode($response->content(), true);

            try {
                $r              = new LogRequest;
                $r->ip          = $ip;
                $r->url         = $url;
                $r->status_code = $response->status();
                $r->method      = $request->method();
                $r->api_name    = isset($api_name[0]) ? $api_name[0] : $api_name;
                $r->method_name = isset($api_name[1]) ? $api_name[1] : $api_name;
                $r->request     = json_encode($request->all());
                $r->response    = ! empty($response) ? json_encode($response) : json_encode([]);
                $r->message     = isset($response_array['message']) ? $response_array['message'] : '';
                $r->start_time  = $startTime;
                $r->end_time    = $endTime;
                $r->time_taken  = $timeTaken;

                $r->save();
            } catch (\Exception $e) {
                \Log::info('Log after request has issue ' . $e->getMessage());
            }
        }
    }
}
