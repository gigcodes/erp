<?php

namespace App\Http\Middleware;

use App\LogRequest;
use Closure;

class LogAfterRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $url = $request->fullUrl();
        $ip  = $request->ip();

        $r              = new LogRequest;
        $r->ip          = $ip;
        $r->url         = $url;
        $r->status_code = $response->status();
        $r->method      = $request->method();
        $r->request     = json_encode($request->all());
        $r->response    = !empty($response) ? json_encode($response) : json_encode([]);
        $r->save();
    }
}
