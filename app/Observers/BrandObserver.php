<?php

namespace App\Observers;

use Auth;
use App\User;
use App\Brand;

class BrandObserver
{
    /**
     * Handle the brand "created" event.
     *
     * @return void
     */
    public function created(Brand $brand)
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
    }

    /**
     * Handle the brand "updated" event.
     *
     * @return void
     */
    public function updated(Brand $brand)
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
    }

    /**
     * Handle the brand "deleted" event.
     *
     * @return void
     */
    public function deleted(Brand $brand)
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::find(6);
        }
    }

    /**
     * Handle the brand "restored" event.
     *
     * @return void
     */
    public function restored(Brand $brand)
    {
        //
    }

    /**
     * Handle the brand "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Brand $brand)
    {
        //
    }
}
