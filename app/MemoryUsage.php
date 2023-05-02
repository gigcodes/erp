<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemoryUsage extends Model
{
    protected $table = 'memory_usage';

    protected static function boot()
    {
        parent::boot();
        
        self::creating(function (MemoryUsage $memory) {
            $thresold_limit_for_memory_uses = Setting::where('name', 'thresold_limit_for_memory_uses')->first();

            $allUsers = User::get();

            $updatedData = $memory->getDirty();

            if (($updatedData['used'] / $updatedData['total']) * 100 > $thresold_limit_for_memory_uses->val) {
                foreach ($allUsers as $user) {
                    if ($user->isAdmin() && $user->phone && $user->whatsapp_number) {
                        app(\App\Http\Controllers\WhatsAppController::class)->sendWithWhatsApp($user->phone, $user->whatsapp_number, 'Uses of memory is increse from give limit of ' . $thresold_limit_for_memory_uses->val);
                    }
                }
            }
        });
    }
}
