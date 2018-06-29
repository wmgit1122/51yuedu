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
use Illuminate\Support\Facades\Session;

class RechargeController extends CommonController
{

    public function index(){
        if(!session()->has('user')){
            return redirect('/login');
        }
        $price=request()->route('price');
        $uid=session()->get('user.uid');
        $user_info=get_user_info($uid);
        $list=DB::table('book_pay_package')->where('status',1)->get();
        $tpl_dara=array(
            'list'=>$list,
            'user_info'=>$user_info,
            'price'=>$price,
        );
        return view('recharge.index',$tpl_dara);
    }

}