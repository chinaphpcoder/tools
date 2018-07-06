<?php
namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * 友情链接管理
 */
class Link extends Model{

    /**
     * 获取关联的用户信息
     */
    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id')->select('id', 'name');
    }
}