<?php
/**
 * Created by PhpStorm.
 * User: 369
 * Date: 2017/11/8
 * Time: 14:52
 */
namespace App\Http\Controllers;

use App\Goods;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\RecommendBook;
use App\BookPool;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    const TOKEN = 'd5j87gk5d0n3z1k9n5v2l6y8r';
    public function index(){
        require_once app_path().'/Tools/wechat/lanewechat.php';
        //验证token
//        $this->verify_token();
//        exit;

        $postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"]) ? $GLOBALS["HTTP_RAW_POST_DATA"] : "";
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        Log::info($postObj);
        $Event = $postObj->Event;
        $EventKey = $postObj->EventKey;
        Log::info("Event:".$Event);
        $openid = $postObj->FromUserName;
        Log::info("openid:".$openid);
        $openid = (array)$openid;
        $openid = $openid[0];
        Log::info("openid:".$openid);
        if ($Event == 'subscribe') {
            //当前为关注事件

            //获取用户信息并保存
            $exists_user=get_user_info_by_openid($openid);
            if(empty($exists_user)){
                //注册
                $user_info=\LaneWeChat\Core\UserManage::getUserInfo($openid);
                Log::info($user_info);
                $add_data=array(
                    'openid'=>$openid,
                    'username'=>$user_info['nickname'],
                    'avatar' => $user_info['headimgurl'],
                    'gender' => $user_info['sex'],
                    'city' => $user_info['city'],
                    'updatetime' => time()
                );
                register($add_data);
            }
            //发送消息
            $recommend_list=RecommendBook::leftJoin('book_pool','book_pool.id','=','recommend_book.book_id')
                ->select('book_pool.*')->get();

            $message = "/:v/:v/:v\n";
            if(!empty($recommend_list)){
                $message .= "亲，终于等到你了，推荐几本热门的小说，给你看看哦\n";
                foreach($recommend_list as $val){
                    $message .= "<a href='".url('deal/'.$val->id)."'>$val->book_name</a>\n";
                }
            }
            $message .= '为了方便下次阅读，请置顶公众号';
            \LaneWeChat\Core\ResponseInitiative::text($openid, $message);

            echo 'success';
            exit;
        }
        if ($Event == 'CLICK') {
            if ($postObj->EventKey == 'key_1') {
                //发送客服二维码
                Log::info("EventKey:".$postObj->EventKey);

                //检查今天有没有签到

                if(is_sign_by_openid($openid)){
                    \LaneWeChat\Core\ResponseInitiative::text($openid, '亲，你今天已经签到过了哦');
                }else{
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

                        \LaneWeChat\Core\ResponseInitiative::text($openid, '签到成功书币+50，明日再来');
                    }

                }
            }

            if ($postObj->EventKey == 'key_2') {
                $user_info=get_user_info_by_openid($openid);
                Log::info("上次阅读跳转".$postObj->EventKey);
                $last_read_chapter=DB::table("book_read_log")
                    ->join('book_pool', 'book_read_log.book_id', '=', 'book_pool.id')
                    ->select('book_pool.book_name','book_read_log.book_id','book_read_log.chapter_order')
                    ->where('book_read_log.uid',$user_info->uid)
                    ->orderBy('book_read_log.createtime', 'desc')
                    ->first();
                if(!empty($last_read_chapter)){
                    //跳转到上次阅读页
                    Log::info("上次阅读跳转");
                    $message = "/:v/:v/:v\n点击继续上次阅读哦";
                    $message .= "<a href='".url('read/'.$last_read_chapter->book_id.'/'.$last_read_chapter->chapter_order)."'>$last_read_chapter->book_name</a>\n";
                    \LaneWeChat\Core\ResponseInitiative::text($openid, $message);
                }else{
                    \LaneWeChat\Core\ResponseInitiative::text($openid, '你还没有阅读过噢');
                }
            }
        }

    }

    private function verify_token() {
        $echostr = $_GET['echostr'];
        Log::info("echostr:".$echostr);
        if ($this->checkSignature()) {
            echo $echostr;
            exit;
        } else {
            exit;
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        Log::info("signature:".$signature);
        $timestamp = $_GET["timestamp"];
        Log::info("timestamp:".$timestamp);
        $nonce = $_GET["nonce"];
        Log::info("nonce:".$nonce);

        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode('', $tmpArr);
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function setMenu(){
        require_once app_path().'/Tools/wechat/lanewechat.php';
        //设置菜单
//        $menuList = array(
//            array('id'=>'1', 'pid'=>'',  'name'=>'常规', 'type'=>'', 'code'=>'key_1'),
//            array('id'=>'2', 'pid'=>'1',  'name'=>'点击', 'type'=>'click', 'code'=>'key_2'),
//            array('id'=>'3', 'pid'=>'1',  'name'=>'浏览', 'type'=>'view', 'code'=>'http://www.lanecn.com'),
//            array('id'=>'4', 'pid'=>'',  'name'=>'扫码', 'type'=>'', 'code'=>'key_4'),
//            array('id'=>'5', 'pid'=>'4', 'name'=>'扫码带提示', 'type'=>'scancode_waitmsg', 'code'=>'key_5'),
//            array('id'=>'6', 'pid'=>'4', 'name'=>'扫码推事件', 'type'=>'scancode_push', 'code'=>'key_6'),
//            array('id'=>'7', 'pid'=>'',  'name'=>'发图', 'type'=>'', 'code'=>'key_7'),
//            array('id'=>'8', 'pid'=>'7', 'name'=>'系统拍照发图', 'type'=>'pic_sysphoto', 'code'=>'key_8'),
//            array('id'=>'9', 'pid'=>'7', 'name'=>'拍照或者相册发图', 'type'=>'pic_photo_or_album', 'code'=>'key_9'),
//            array('id'=>'10', 'pid'=>'7', 'name'=>'微信相册发图', 'type'=>'pic_weixin', 'code'=>'key_10'),
//            array('id'=>'11', 'pid'=>'1', 'name'=>'发送位置', 'type'=>'location_select', 'code'=>'key_11'),
//        );
        $menuList = array(
            array('id'=>'1', 'pid'=>'',  'name'=>'上次阅读', 'type'=>'click', 'code'=>'key_2'),
            array('id'=>'2', 'pid'=>'',  'name'=>'访问书城', 'type'=>'', 'code'=>'key_4'),
            array('id'=>'3', 'pid'=>'2',  'name'=>'书城首页', 'type'=>'view', 'code'=>'http://read.iyuebang.com/'),
            array('id'=>'4', 'pid'=>'2',  'name'=>'免费专区', 'type'=>'view', 'code'=>'http://read.iyuebang.com/free/'),
            array('id'=>'5', 'pid'=>'', 'name'=>'用户中心', 'type'=>'', 'code'=>'key_5'),
            array('id'=>'6', 'pid'=>'5', 'name'=>'个人中心', 'type'=>'view', 'code'=>'http://read.iyuebang.com/user/'),
            array('id'=>'7', 'pid'=>'5', 'name'=>'我要充值', 'type'=>'view', 'code'=>'http://read.iyuebang.com/recharge/'),
            array('id'=>'8', 'pid'=>'5', 'name'=>'阅读记录', 'type'=>'view', 'code'=>'http://read.iyuebang.com/user/readlog/'),
            array('id'=>'9', 'pid'=>'5', 'name'=>'每日签到', 'type'=>'click', 'code'=>'key_1'),
        );
        $res=\LaneWeChat\Core\Menu::setMenu($menuList);
        dd($res);
        return $res;
    }

}