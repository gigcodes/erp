<?php

namespace App;

use App\Models\UicheckHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uicheck extends Model
{
    use SoftDeletes;

    protected $table = 'uichecks';

    protected $fillable = [
        'id',
        'site_development_category_id',
        'site_development_id',
        'website_id',
        'issue',
        'communication_message',
        'dev_status_id',
        'admin_status_id',
        'start_time',
        'expected_completion_time',
        'actual_completion_time',
        'lock_developer',
        'lock_admin',
        'language_flag',
        'translation_flag',
        'uicheck_type_id',
    ];

    public function whatsappAll($needBroadCast = false)
    {
        if ($needBroadCast) {
            return $this->hasMany(\App\ChatMessage::class, 'document_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(\App\ChatMessage::class, 'document_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function updateElement($type, $newValue)
    {
        $oldValue = $this->$type;
        if ($oldValue != $newValue) {
            $this->$type = $newValue;
            $this->save();
            UicheckHistory::create([
                'uichecks_id' => $this->id,
                'type' => $type,
                'old_val' => $oldValue,
                'new_val' => $newValue,
                'user_id' => \Auth::id(),
            ]);
        }
    }

    public function uiDevice()
    {
        return $this->hasMany(UiDevice::class, 'uicheck_id', 'id');
    }

    public function uiDeviceCount($userId)
    {
        return $this->hasMany(UiDevice::class, 'uicheck_id', 'id')->where('user_id', $userId)->count();
    }
}
