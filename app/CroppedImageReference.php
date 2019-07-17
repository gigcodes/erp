<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Media;

class CroppedImageReference extends Model
{
    public function media() {
        return $this->hasOne(Media::class, 'id', 'original_media_id');
    }
}
