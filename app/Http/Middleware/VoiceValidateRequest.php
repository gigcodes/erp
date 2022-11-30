<?php

namespace App\Http\Middleware;

use App\TwilioCallBlock;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;

class VoiceValidateRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     *
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $number = $request->get('Caller');

        $isBlock = TwilioCallBlock::where('customer_number', $number)->count();

        if ($isBlock) {
            $response = new VoiceResponse();
            $response->say('The number you are dialing is currently busy. Please try again later');
            $response->reject();

            return response($response)->header('Content-Type', 'text/xml');
        }

        return $next($request);
    }
}
