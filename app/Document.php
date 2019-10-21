<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\DocumentCategory;
use Storage;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'filename',
        'category_id',
        'version',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getDocumentPathById($id)
    {
        $document = $this::find($id);
        return Storage::disk('files')->url('documents/' . $document->filename);
    }

    public function documentCategory()
    {
        return $this->hasOne(DocumentCategory::class, 'id', 'category_id');
    }
}
