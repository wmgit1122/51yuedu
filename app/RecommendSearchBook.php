<?php
/**
 * User: 敏敏
 * Date: 2018/3/29
 * Time: 10:23
 */
namespace  App;

use Illuminate\Database\Eloquent\Model;

class RecommendSearchBook extends Model{

    protected $table='recommend_search_book';

    protected $primaryKey='id';

    public function book_info(){
        return $this->hasOne('App\BookPool', 'id','book_id');
//        return $this->belongsToMany('App\BookPool','user_role','user_id','role_id');
    }

}