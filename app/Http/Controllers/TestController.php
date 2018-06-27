<?php
/**
 * Created by PhpStorm.
 * User: 369
 * Date: 2017/11/8
 * Time: 14:52
 */
namespace App\Http\Controllers;

use App\Goods;
use App\Jobs\SendReminderEmail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\RecommendBook;
use App\BookPool;
use Illuminate\Support\Facades\Mail;
use Redis;

class TestController extends Controller
{
    private  $_redis;
    public function __construct(){
        $this->_redis=new Redis();
        $this->_redis->connect('127.0.0.1','6379');
        $this->_redis->auth('w111111');
    }
    public function index(){

        $this->_redis->delete("string1");
        $this->_redis->set('string1','val1');
        $val=$this->_redis->get('string1');

        $this->_redis->set('int1',1);
        $this->_redis->incr('int1');
        $val=$this->_redis->get('int1');
        dd($val);
    }

    public function redis_list(){
        $this->_redis->delete("list1");
        $this->_redis->lPush('list1','A');
        $this->_redis->lPush('list1','B');
        $this->_redis->lPush('list1','A');//取出则删除
        $val=$this->_redis->rPop('list1');
        dd($val);
    }

    public function redis_set(){
        $this->_redis->delete("set1");
        $this->_redis->sAdd("set1","A");
        $this->_redis->sAdd("set1","B");
        $this->_redis->sAdd("set1","C");
        $this->_redis->sAdd("set1","C");
        $val=$this->_redis->sCard("set1");
        $val=$this->_redis->sMembers("set1");
        dd($val);
    }

    public function redis_hash(){
        $this->_redis->delete("hash1");
        $this->_redis->hSet("hash1","name","liming");
        $this->_redis->hSet("hash1","age",26);
        $this->_redis->hSet("hash1","gender",1); //1代表男
        $val=$this->_redis->hGet("hash1",'name');
        dd($val);
    }

    public function sort_set(){
        $this->_redis->delete("sort1");
        $this->_redis->zAdd("sort1",100,"xiaoming");
        $this->_redis->zAdd("sort1",60,"xiaohong");
        $this->_redis->zAdd("sort1",90,"xiaohua");
    }

    public function queue(){
        dispatch(new SendReminderEmail('1527460876@qq.com'));
    }

    public function mail(){
//        Mail::raw('邮件内容',function($message){
//            $message->from('289717197@qq.com','小王子');
//            $message->subject('测试邮件系统');
//            $message->to('1527460876@qq.com');
//        });
//        Mail::send('mail.index',['name'=>'wangmin'],function($message){
//            $message->to('1527460876@qq.com');
//        });
    }

    public function redis_test(){
        Redis::set('name', 'Taylor');
        $result = Redis::get('name');
        dd($result);
    }
}