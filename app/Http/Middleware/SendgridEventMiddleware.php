<?php

namespace App\Http\Middleware;

use Closure;

class SendgridEventMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->isKeyValid($request)) {
            return response()->json(['message' => 'Unauthorized request!'], 401);
        }

        return $next($request);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isKeyValid($request)
    {
        if (! config('sendgridevents.url_secret_key')) {
            return true;
        }
        if ($request->input('key') == config('sendgridevents.url_secret_key')) {
            return true;
        }

        return false;
    }
}
