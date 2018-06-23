<?php

namespace  App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //指定表名
    protected  $table ='user';

    //指定id
    protected $primaryKey='uid';

    public $timestamps=false;

    protected $fillable = ['username', 'avatar', 'gender', 'city', 'book_coin', 'auto_consume', 'login_type', 'updatetime', 'createtime', 'openid'];
}