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
use App\RecommendSearchBook;

class SearchController extends CommonController
{

    public function index(Request $request){
        $uid=session()->get('user.uid');
        $method=1;
        $user_info=get_user_info($uid);
        $search_hot=DB::table("search_hot")
            ->join('book_pool', 'search_hot.book_id', '=', 'book_pool.id')
            ->where('book_pool.shelf_status',1)
            ->select('book_pool.*')
            ->get();
        if($request->isMethod('post')){
            $method=2;
            $title=$request->input('title');
            $list=DB::table('book_pool')
                ->where('book_pool.book_name', 'like', '%'.$title.'%')
                ->where('book_pool.shelf_status',1)
                ->select('book_pool.*')->get();

        }else{
            $list=RecommendSearchBook::leftJoin('book_pool','book_pool.id','=','recommend_search_book.book_id')
                ->where('book_pool.shelf_status',1)
                ->select('book_pool.*')->get();
        }
        $tpl_dara=array(
            'user_info'=>$user_info,
            'search_hot'=>$search_hot,
            'list'=>$list,
            'method'=>$method,
        );
        return view('search.index',$tpl_dara);
    }

}