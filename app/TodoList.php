<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    protected $table = 'todo_lists';

    protected $fillable = ['id', 'user_id', 'title', 'status', 'todo_date', 'remark', 'created_at', 'updated_at', 'todo_category_id'];

    public function username()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->hasOne(TodoCategory::class, 'id', 'todo_category_id');
    }

    public function color()
    {
        return $this->belongsTo(TodoStatus::class, 'status');
    }
}
