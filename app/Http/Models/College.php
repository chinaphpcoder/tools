<?php
namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * 友情链接管理
 */
class College extends Model{

    /**
     * 获取关联的用户信息
     */
    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * 获取关联的文章类型
     */
    public function type(){
        return $this->hasOne('App\Http\Models\ArticleType', 'id', 'type_id')->where('type_id', 1);
    }
}