<?php
/**
 * User: 敏敏
 * Date: 2018/3/29
 * Time: 15:42
 */
namespace App\Http\Controllers;

use App\Goods;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\RecommendBook;
use App\BookPool;
use App\User;
use Log;



/**
 * 基类
 * Class CommonController
 * @package App\Http\Controllers
 */
class CommonController extends Controller
{
    public function __construct(){
       //用户进来判断用户有没有去微信授权
        if(!session()->has('user')){
            require_once app_path().'/Tools/wechat/lanewechat.php';
            $redirect_uri ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];
            if(!isset($_GET['code'])){
                /**
                 * 网页授权
                 */
                /**
                 * Description: 获取CODE
                 * @param $scope snsapi_base不弹出授权页面，只能获得OpenId;snsapi_userinfo弹出授权页面，可以获得所有信息
                 * 将会跳转到redirect_uri/?code=CODE&state=STATE 通过GET方式获取code和state
                 */
                $redirect_uri ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];
                \LaneWeChat\Core\WeChatOAuth::getCode($redirect_uri, $state=1, $scope='snsapi_userinfo');//授权
            }else {
                /**
                 * Description: 通过code换取网页授权access_token
                 * 首先请注意，这里通过code换取的网页授权access_token,与基础支持中的access_token不同。
                 * 公众号可通过下述接口来获取网页授权access_token。
                 * 如果网页授权的作用域为snsapi_base，则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
                 * @param $code getCode()获取的code参数
                 */

                $code = $_GET['code'];
                $res = \LaneWeChat\Core\WeChatOAuth::getAccessTokenAndOpenId($code);
    
                //获取用户信息
                $user_info = \LaneWeChat\Core\UserManage::getUserInfo2($res['openid'], $res['access_token']);

                //检查用户有没有注册过
                $user=User::where('openid', $user_info['openid'])->first();
                if(empty($user)){
                    $adddata=array(
                        'openid'=>$user_info['openid'],
                        'username'=>$user_info['nickname'],
                        'avatar'=>$user_info['headimgurl'],
                        'avatar'=>$user_info['headimgurl'],
                        'gender'=>$user_info['sex'],
                        'city'=>$user_info['city'],
                        'createtime'=>time(),
                    );

                    User::create($adddata);
                    $user=User::where('openid', $user_info['openid'])->first();
                    session()->put('user',$user);
                }else{
                    session()->put('user',$user);
                }

            }
        }

    }

}