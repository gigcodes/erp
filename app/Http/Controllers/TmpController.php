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
                
                if (!empty($mediables->mediable_id) && $mediables->mediable_id > 1) {
                    $key   = floor($mediables->mediable_id/10000);
                } else {
                    $key   = strtolower(substr($media->basename,0,1).'/'.substr($media->basename,1,1));
                }

                if ($media->getDiskPath() != $table.'/'.$key.'/'.ltrim($media->basename, '/')) {
                    $media->move($table.'/'.$key);
                }
            }
        }
    }
}
