<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function type()
    {
        return $this->hasOne('App\Http\Models\ArticleType', 'id', 'type_id')->where('type_id', 3);
    }
}