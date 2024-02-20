<?php

namespace App\Http\Middleware;

use Auth;
use Cache;
use Closure;
use App\UserLogin;
use Carbon\Carbon;
use Illuminate\Session\Store;

class LogLastUserActivity
{
    protected $timeout = 1800;

    public function __construct(protected Store $session)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            $cacheKey = 'user-is-online-' . Auth::user()->id;

            // cache with 5 min expiry.
            $lastLogin = Cache::has($cacheKey, true, $expiresAt);

            // if cache doesn't exists, add cache and db entry.
            if (! $lastLogin) {
                // else add cache and add in db.
                Cache::put($cacheKey, true, $expiresAt);

                UserLogin::create([
                    'user_id' => Auth::id(),
                    'login_at' => Carbon::now(),
                ]);
            }
        }

        if (! $this->session->has('lastActivityTimeU')) {
            $this->session->put('lastActivityTimeU', time());
        } elseif (time() - $this->session->get('lastActivityTimeU') > $this->getTimeOut()) {
            $this->session->forget('lastActivityTimeU');
            $display = \Carbon\CarbonInterval::seconds($this->getTimeOut())->cascade()->forHumans();
            if ($user_login = UserLogin::where('user_id', Auth::id())->latest()->first()) {
                if (Carbon::now()->diffInDays($user_login->logout_at) == 0) {
                    $user_login->update(['logout_at' => Carbon::now()]);
                } else {
                    UserLogin::create([
                        'user_id' => Auth::id(),
                        'logout_at' => Carbon::now(),
                    ]);
                }
            }
            Auth::logout();

            return redirect('/login')->withErrors(['You have been inactive for ' . $display . '']);
        }
        $this->session->put('lastActivityTimeU', time());

        return $next($request);
    }

    protected function getTimeOut()
    {
        if (Auth::user()) {
            $timeout = (Auth::user()->user_timeout != 0) ? Auth::user()->user_timeout : $this->timeout;
        } else {
            $timeout = $this->timeout;
        }

        return $timeout;
    }
}
