<?php

namespace App\Listeners;

use App\Http\Controllers\ActivityConroller;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLoginListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle()
    {
//	    activity()->performedOn(\App\User::getModel())->withProperties(['type' => 'info'])->log('Login');
	    ActivityConroller::create(0,'User','Login');

    }
}
