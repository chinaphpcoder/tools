<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $table = 'cms_advertisement';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'admin_user_id')->select('id', 'name');
    }

    public function position()
    {
        return $this->hasMany("App\Http\Models\AdvertisementPosition",  'ad_id' ,'id')->select('display_position');
    }

}
