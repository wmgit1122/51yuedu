<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Yansongda\Pay\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    protected $config = [
        'wechat' => [
            'app_id' => 'wx793c4440ee8af003',
            'mch_id' => '1276673001',
            'notify_url' => 'http://read.iyuebang.com/wechat_notify/',
            'key' => '8934e7d15453e97507ef794cf7b05256',
            'cert_client' => '',
            'cert_key' => '',
        ],
    ];

    public function index(Request $request)
    {
        Log::info('微信支付请求');
        $pid=$request->input('pid');
        //获取套餐信息
        $book_pay_package_info=DB::table('book_pay_package')->where('id',$pid)->first();
        if(empty($book_pay_package_info)){
            return response()->json(array(
                'code'=>'0',
                'msg'=>'不存在',
                'data'=>array(),
            ));
        }
        $order_no =build_order_no();
        $config_biz = [
            'out_trade_no' => $order_no,
            'total_fee' => $book_pay_package_info->price*100,
            'body' => '书币套餐',
            'spbill_create_ip' => $request->getClientIp(),
            'openid' => session()->get('user.openid'),
            'attach'=>$pid,//商品id
        ];
        //统一下单
        $order_info=array(
            'uid'=> session()->get('user.uid'),
            'pid'=>$pid,
            'order_sn'=>$order_no,
            'price'=>$book_pay_package_info->price,
            'book_coin'=>$book_pay_package_info->book_coin,
            'add_time'=>time(),
        );
        DB::table('t_order_info')->insert($order_info);
        $pay = new Pay($this->config);
        return $pay->driver('wechat')->gateway('mp')->pay($config_biz);
    }

    public function notify(Request $request)
    {
        Log::info('收到来自微信的异步通知');
        $pay = new Pay($this->config);
        $verify = $pay->driver('wechat')->gateway('mp')->verify($request->getContent());
        if ($verify) {
            Log::info('订单号:'.$verify['out_trade_no']);
            Log::info('订单金额:'.$verify['total_fee']);
            Log::info($verify);
            if($verify['result_code']=='SUCCESS'){
                $order_sn=$verify['out_trade_no'];
                //更新订单状态
                $order_info=DB::table('t_order_info')->where('order_sn',$order_sn)->first();
                DB::table('t_order_info')->where('order_sn',$order_sn)->update(
                    array(
                        'order_status'=>1,
                        'pay_time'=>time(),
                    )
                );
                //给用户加书币
                user_addcoin($order_info->uid,$order_info->price,$order_info->book_coin);
            }

        } else {
            echo "error";
            Log::info($verify);
        }

        echo "success";
    }
}