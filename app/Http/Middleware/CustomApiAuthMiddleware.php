<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BearerAccessTokens;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CustomApiAuthMiddleware extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (! $request->bearerToken()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $bearerToken = $request->bearerToken();
            $bearerTokenModel = new BearerAccessTokens();

            if ($bearerAccessToken = $bearerTokenModel->getByToken($bearerToken)) {
                $user = $bearerAccessToken->getUser();

                if ($user === null) {
                    throw new \Exception('User not found.');
                }

                auth()->login($user);
            } else {
                throw new \Exception('Token not found or expired. Generate new token!');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
