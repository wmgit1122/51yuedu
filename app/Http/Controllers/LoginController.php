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
use App\Lib\Response;
use Illuminate\Support\Facades\Session;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendReminderEmail;
class LoginController extends CommonController
{
    public function index(Request $request){
        if(session()->has('user')){
            return redirect('/');
        }
        if($request->Method()=='POST'){
            $email=$request->input('email');
            $code=$request->input('code');
            //判断code 是否正确
            $where= array(
                'eamil'=>$email,
                'code'=>$code,
            );

            $code_info=DB::table('t_sms_code')->where(
                $where
            )->first();
            if(empty($code_info)){
                Response::show(0,'验证码错误');
            }
            //检查用户有没有注册过
            $user=User::where('email', $email)->first();
            if(empty($user)){
                $adddata=array(
                    'username'=>$email,
                    'avatar'=>'http://www.qing01.com/public/img/header_img.png',
                    'email'=>$email,
                    'createtime'=>time(),
                );
                User::create($adddata);
                $user=User::where('email', $email)->first();
                Session::put('user',$user);
		        Session::save();
            }else{
                Session::put('user',$user);
		        Session::save();
            }
            Response::show(1,'登录成功');
        }
        $tpl_dara=[];
        return view('login.index',$tpl_dara);
    }

    public function get_code(Request $request){
        //把邮箱添加到队列里
        $email=$request->input('email');
        if(empty($email)){
            Response::show(0,'发送失败');
        }
        $code=rand(100000,999999);
        $adddata=array(
            'eamil'=>$email,
            'code'=>$code,
            'sendtime'=>time(),
        );
        $insert_id=DB::table('t_sms_code')->insertGetId($adddata);
        if($insert_id){
            //加入队列
            dispatch(new SendReminderEmail($email,$code));
           return Response::show(1,'发送成功');
        }else{
            return  Response::show(0,'发送失败');
        }
    }
}