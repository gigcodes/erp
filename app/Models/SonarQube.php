<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SonarQube extends Model
{
    use HasFactory;

    public $fillable = [
        'key',
        'rule',
        'severity',
        'component',
        'project',
        'hash',
        'textRange',
        'flows',
        'resolution',
        'status',
        'message',
        'effort',
        'debt',
        'author',
        'tags',
        'creationDate',
        'updateDate',
        'closeDate',
        'type',
        'scope',
        'quickFixAvailable',
        'messageFormattings',
        'codeVariants',
    ];
}