<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $table = 'cms_notice';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}