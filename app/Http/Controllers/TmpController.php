<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Plank\Mediable\Media;

class TmpController extends Controller
{

    public function updateImageDirectory()
    {
        $mediaArr = Media::paginate(100);
        foreach ($mediaArr as $media) {
            if ($media->fileExists()) {
                $mediables = DB::table('mediables')->where('media_id', $media->id)->first();
                $table = strtolower(str_replace('App\\', '', $mediables->mediable_type));
                $key   = floor($mediables->mediable_id/10000);
                if ($media->getDiskPath() != $table.'/'.$key.'/'.ltrim($media->basename, '/')) {
                    $media->move($table.'/'.$key);
                }
            }
        }
    }
}
