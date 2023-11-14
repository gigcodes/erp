<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CustomApiAuthMiddleware extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (!$request->bearerToken()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $bearerToken = $request->bearerToken();

            if ($bearerAccessToken = \DB::table('bearer_access_tokens')->select('*')->where('token', $bearerToken)->first()) {
                $user = \App\User::find($bearerAccessToken->user_id);

                if ($user === null) {
                    throw new \Exception('User not found.');
                }

                auth()->login($user);
            } else {
                throw new \Exception('Token not found.');
            }
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}