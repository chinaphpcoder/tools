<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table = 'article_story';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Http\Models\Comments', 'article_story_id', 'id');
    }

}
