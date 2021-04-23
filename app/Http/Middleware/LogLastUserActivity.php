<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Cache;
use App\UserLogin;
use Carbon\Carbon;
use Illuminate\Session\Store;

class LogLastUserActivity
{
    protected $session;
    protected $timeout = 10000*60;
    public function __construct(Store $session){
        $this->session=$session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if(Auth::check()) {
          $expiresAt = Carbon::now()->addMinutes(5);
          Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);

          // if ($user_login = UserLogin::where('user_id', Auth::id())->latest()->first()) {
          //   if (Carbon::now()->diffInDays($user_login->login_at) != 0) {
          //     UserLogin::create([
          //       'user_id'   => Auth::id(),
          //       'login_at' => Carbon::now()
          //     ]);
          //   }
          // } else {
          //   UserLogin::create([
          //     'user_id'   => Auth::id(),
          //     'login_at' => Carbon::now()
          //   ]);
          // }
      }

      if(!$this->session->has('lastActivityTime'))
            $this->session->put('lastActivityTime',time());
        elseif(time() - $this->session->get('lastActivityTime') > $this->getTimeOut()){
            $this->session->forget('lastActivityTime');
            // if ($user_login = UserLogin::where('user_id', Auth::id())->latest()->first()) {
            //   if (Carbon::now()->diffInDays($user_login->logout_at) == 0) {
            //     $user_login->update(['logout_at' => Carbon::now()]);
            //   } else {
            //     UserLogin::create([
            //       'user_id'   => Auth::id(),
            //       'logout_at' => Carbon::now()
            //     ]);
            //   }
            // } else {
            //   UserLogin::create([
            //     'user_id'   => Auth::id(),
            //     'logout_at' => Carbon::now()
            //   ]);
            // }
            Auth::logout();
            return redirect('/login')->withErrors(['You have been inactive for 30 minutes']);
        }
        $this->session->put('lastActivityTime',time());

      return $next($request);
    }

    protected function getTimeOut()
    {
      return (env('TIMEOUT')) ?: $this->timeout;
    }
}
