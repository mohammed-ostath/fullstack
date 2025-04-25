<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'user_id'
    ];

    // public function comments()
    // {
    //     return $this->hasMany(Comment::class);
    // }

    public function comments() {

        return $this->morphMany(Comment::class, 'commentable');
    }
}
