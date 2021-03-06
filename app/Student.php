<?php
/**
 * Created by PhpStorm.
 * User: 369
 * Date: 2017/11/7
 * Time: 15:38
 */
namespace  App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    const SEX_UN = 10;
    const SEX_BOY = 20;
    const SEX_GIRL = 30;
    //指定表名
    protected  $table ='student';

    //指定id
    protected $primaryKey='id';

    //指定允许赋值的字段
    protected $fillable=['name','age'];

    //指定不允许批量赋值的字段
    protected $guarded=[];
    //
    public $timestamps=true;

    protected function getDateFormat()
    {
        return time(); // TODO: Change the autogenerated stub
    }

    protected function asDateTime($value)
    {
        return $value; // TODO: Change the autogenerated stub
    }


    public function sex($ind=NULL) {
        $arr = [
            self::SEX_UN => '未知',
            self::SEX_BOY => '男',
            self::SEX_GIRL => '女',
        ];
        if ($ind!==NULL) {
            return array_key_exists($ind,$arr)?$arr[$ind]:$arr[self::SEX_UN];
        }
        return $arr;
    }

}