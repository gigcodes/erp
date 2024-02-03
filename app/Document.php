<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Storage;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * @SWG\Property(property="user_id",type="integer")
     * @SWG\Property(property="name",type="string")
     * @SWG\Property(property="filename",type="string")
     * @SWG\Property(property="category_id",type="integer")
     * @SWG\Property(property="version",type="string")
     * @SWG\Property(property="status",type="string")
     */
    protected $fillable = [
        'user_id',
        'name',
        'filename',
        'file_contents',
        'category_id',
        'version',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getDocumentPathById($id)
    {
        $document = $this::find($id);

        return Storage::url('files/documents/' . $document->filename);
    }

    public function documentCategory()
    {
        return $this->hasOne(DocumentCategory::class, 'id', 'category_id');
    }

    public function whatsappAll($needBroadCast = false)
    {
        if ($needBroadCast) {
            return $this->hasMany(\App\ChatMessage::class, 'document_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany(\App\ChatMessage::class, 'document_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }

    public function allMessages()
    {
        return $this->hasMany(ChatMessage::class, 'document_id', 'id')->orderBy('id', 'desc');
    }
}
