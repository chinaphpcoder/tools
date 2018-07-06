<?php
namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * 文章类型
 */
class ArticleType extends Model{

    /**
     * 获取关联的用户信息
     */
    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id')->select('id', 'name');
    }
}