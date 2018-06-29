<?php
/**
 * User: 敏敏
 * Date: 2018/3/29
 * Time: 15:41
 */
use Illuminate\Support\Facades\Redis;

function DeleteHtml($str)
{
    $str = trim($str); //清除字符串两边的空格
    $str = strip_tags($str, ""); //利用php自带的函数清除html格式
    $str = preg_replace("/\t/", "", $str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
    $str = preg_replace("/\r\n/", "", $str);
    $str = preg_replace("/\r/", "", $str);
    $str = preg_replace("/\n/", "", $str);
    $str = preg_replace("/ /", "", $str);
    $str = preg_replace("/  /", "", $str);  //匹配html中的空格
    return trim($str); //返回字符串
}

function register($user){
   return DB::table('user')->insertGetId(
        $user
    );

}


function get_user_info_by_openid($openid){
    return DB::table('user')->where(array(
        'openid'=>$openid,
    ))->first();
}

function is_sign_by_openid($openid){
    $date=date('Y-m-d');
    $sql="select * from sign_log where DATE_FORMAT(FROM_UNIXTIME(addtime),'%Y-%m-%d') ='".$date."' and openid='".$openid."'";
    $results = DB::select($sql);
    return $results;
}

function isadd_bookshelf($book_id,$chapter_order,$uid){
    $exists=DB::table('bookshelf')->where(array(
        'book_id'=>$book_id,
        'chapter_order'=>$chapter_order,
        'uid'=>$uid,
    ))->first();
    if(!$exists){
        $add_data=array(
            'book_id'=>$book_id,
            'chapter_order'=>$chapter_order,
            'uid'=>$uid,
            'addtime'=>time(),
        );
        DB::table('bookshelf')->insertGetId(
            $add_data
        );
        return true;
    }else{
        return false;
    }
}


function get_category_info($category_id){
    $info=DB::table('book_category')->where('category_id', $category_id)->first();
    return $info;
}


function get_category_count($category_id){
     return DB::table('book_pool')->where('category_id',$category_id)->where(array(
        'isdelete'=>0,
        'shelf_status'=>1,
    ))->count();
}


function get_user_info($uid){
    $user=DB::table('user')->where('uid', $uid)->first();
    return $user;
}

/**
 * 返回平台唯一订单号
 * @return string
 */
function  build_order_no(){
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}


function is_free($book_id){
    $time=time();
    $res=DB::table("book_free_list")
        ->join('book_free', 'book_free_list.ext_id', '=', 'book_free.id')
        ->select('book_free_list.*')
        ->where('book_free.isdelete',0)
        ->where('book_free.starttime', '<', $time)
        ->where('book_free.endtime', '>', $time)
        ->where('book_free_list.book_id', $book_id)
        ->get();
    if(empty($res)){
        return false;
    }
    return true;
}


function format_date($time){
    $t=time()-$time;
    $f=array(
        '31536000'=>'年',
        '2592000'=>'个月',
        '604800'=>'星期',
        '86400'=>'天',
        '3600'=>'小时',
        '60'=>'分钟',
        '1'=>'秒'
    );
    foreach ($f as $k=>$v)    {
        if (0 !=$c=floor($t/(int)$k)) {
            return $c.$v.'前';
        }
    }
}



function add_read_log($uid,$book_id,$chapter_order)
{
//    $exists=DB::table("book_read_log")->where(array(
//        'uid'=>$uid,
//        'book_id'=>$book_id,
//        'chapter_order'=>$chapter_order,
//    ))->first();
//    if(empty($exists)){
        DB::table("book_read_log")->insert(
            array(
                'uid'=>$uid,
                'book_id'=>$book_id,
                'chapter_order'=>$chapter_order,
                'createtime'=>time(),
            )
        );
//    }
}


function user_addcoin($uid,$price,$coin){
    $status=DB::table('user')->where('uid',$uid)->increment('book_coin', $coin);
    if($status){
        //插入消费记录
        DB::table("recharge_log")->insert(
            array(
                'uid'=>$uid,
                'coin'=>$coin,
                'price'=>$price,
                'createtime'=>time(),
            )
        );
    }
}

function buy_chapter($uid,$book_id,$chapter_order){
    $table_name='book_chapter_'.$book_id%20;
    $info=DB::table($table_name)->where('chapter_order',$chapter_order)->where('book_id',$book_id)->first();
    if($info->ischarge){
        $price=$info->price;
        //查看是否已经购买过
        $has_pay=DB::table('book_coin_log')->where(
            array(
                'uid'=>$uid,
                'book_id'=>$book_id,
                'chapter_order'=>$chapter_order,
            )
        )->first();
        if(!empty($has_pay)){
            return true;
        }
        //付费扣费
        $user_info=DB::table('user')->where('uid',$uid)->first();

        if($user_info->book_coin < $price){
            return false;//去充值
        }
        $status=DB::table('user')->where('uid',$uid)->decrement('book_coin', $price);
        if($status){
            //插入消费记录
            DB::table("book_coin_log")->insert(
                array(
                    'uid'=>$uid,
                    'book_id'=>$book_id,
                    'chapter_order'=>$chapter_order,
                    'price'=>$price,
                    'createtime'=>time(),
                )
            );
            return true;
        }
    }
    return true;
}

function get_chapter_order_price($book_id,$chapter_order){
    $table_name='book_chapter_'.$book_id%20;
    $info=DB::table($table_name)->where('chapter_order',$chapter_order)->where('book_id',$book_id)->first();
    return   $info->price;
}

function auto_sign($openid){
    return false;
    require_once app_path().'/Tools/wechat/lanewechat.php';
    //自动签到
    //检查今天有没有签到
    if(!is_sign_by_openid($openid)){
        $add_data=array(
            'openid'=>$openid,
            'addtime'=>time(),
        );
        $insert_id=DB::table('sign_log')->insertGetId(
            $add_data
        );
        if(!empty($insert_id)){
            //送书币
            $user_info=get_user_info_by_openid($openid);
            $add_data=array(
                'uid'=>$user_info->uid,
                'type'=>'1',
                'num'=>50,
                'addtime'=>time(),
                'extid'=>$insert_id
            );
            $insert_id=DB::table('book_free_coin_log')->insertGetId(
                $add_data
            );
            if(!empty($insert_id)){
                DB::table('user')->where('uid',$user_info->uid)->increment('book_coin', 50);
            }

            $list=DB::table("book_read_log")
                ->join('book_pool', 'book_read_log.book_id', '=', 'book_pool.id')
                ->select('book_pool.*')
                ->orderBy('book_read_log.createtime', 'desc')
                ->groupBy('book_pool.id')
                ->take(5)
                ->where(array(
                    'uid'=>$user_info->uid,
                ))->get();

            //最近阅读的图书
            $last_read_chapter=DB::table("book_read_log")
                ->join('book_pool', 'book_read_log.book_id', '=', 'book_pool.id')
                ->select('book_pool.book_name','book_read_log.book_id','book_read_log.chapter_order')
                ->where('book_read_log.uid',session()->get('user.uid'))
                ->orderBy('book_read_log.createtime', 'desc')
                ->first();

            $message = "/:v/:v/:v\n";
            $message .= "本日签到成功，赠送50书币, 请明日继续签到哦~\n";
            if(!empty($last_read_chapter)){
                $message .= "<a href='".url('read/'.$last_read_chapter->book_id.'/'.$last_read_chapter->chapter_order)."'>点我继续上次阅读</a>\n";
            }
            if(!empty($list)){
                $message .= "历史阅读记录\n";
                foreach($list as $val){
                    $message .= "<a href='".url('deal/'.$val->id)."'>$val->book_name</a>\n";
                }
            }
            $message .= '为了方便下次阅读，请置顶公众号';
            $sign_res=\LaneWeChat\Core\ResponseInitiative::text($openid, $message);
            Log::info("Common_WeChat_sign_res:");
            Log::info($sign_res);
            return true;
        }
    }
    else{
        return false;
    }
}

function get_chapter_order_title($book_id,$chapter_order){
    $table_name='book_chapter_'.$book_id%20;
    $res=DB::table($table_name)->where(
        array(
            'chapter_order'=>$chapter_order,
            'book_id'=>$book_id,
        )
    )->first();
    return $res->title;
}


function get_category_by_book_id($book_id){
    $res=DB::table('book_pool')->where('id',$book_id)->first();
    if(!empty($res)){
        return $res->category_id;
    }else{
        return 0;
    }

}

function get_like_books($uid){
    $read_log=DB::table('book_read_log')->where('uid',$uid)->groupBy('book_id')->get();
    $category_arr=array();
    foreach($read_log as $key=>$val){
        $category=get_category_by_book_id($val->book_id);
        if(!empty($category)){
            if(!in_array($category,$category_arr)){
                $category_arr[]=$category;
            }
        }
    }
    if($category_arr){
        $book_list=DB::table('book_pool')->where('shelf_status',1)->whereIn('category_id', $category_arr)->orderBy(DB::raw('RAND()'))->take(6)->get();
    }else{
        $book_list=array();
    }

    return $book_list;
}

function get_book_deal($id){
    $redis_key='book_id_'.$id;
    if(empty(Redis::get($redis_key))){
        $deal=BookPool::where('id', $id)->first();
        Redis::setex($redis_key,config('common.redis_timeout'),serialize($deal));
    }else{
        $deal=unserialize(Redis::get($redis_key));
    }
    return $deal;
}