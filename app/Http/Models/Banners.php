<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Banners extends Model
{
    protected $table = 'banners';

    /**
     * 关联用户信息
     * @author 周瑶
     * @date 2017/11/11
     * @return mixed
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->select('id', 'name');
    }
}
