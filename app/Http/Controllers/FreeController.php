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

class FreeController extends Controller
{

    public function index(){
        $uid=session()->get('user.uid');
        $time=time();
        $user_info=get_user_info($uid);
        $list=DB::table("book_free_list")
            ->join('book_pool', 'book_free_list.book_id', '=', 'book_pool.id')
            ->join('book_free', 'book_free_list.ext_id', '=', 'book_free.id')
            ->select('book_pool.*','book_free.starttime','book_free.endtime')
            ->where('book_free.isdelete',0)
            ->where('book_free.starttime', '<', $time)
            ->where('book_free.endtime', '>', $time)
            ->get();
        $tpl_dara=array(
            'list'=>$list,
            'user_info'=>$user_info,
        );
        return view('free.index',$tpl_dara);
    }

}