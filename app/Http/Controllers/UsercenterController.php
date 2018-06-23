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

class UsercenterController extends CommonController
{

    //首页
    public function index(){
        $uid=session()->get('user.uid');
        $user=DB::table('user')->where('uid',$uid)->first();
        $tpl_dara=array(
            'user'=>$user,
        );
        return view('user_center.index',$tpl_dara);
    }

    public function readlog(Request $request){
        $uid=session()->get('user.uid');
        $page=$request->input('page',1);
        $pagesize=$request->input('pagesize',10);
        $start=($page-1)*$pagesize;
        $list=DB::table("book_read_log")
            ->join('book_pool', 'book_read_log.book_id', '=', 'book_pool.id')
            ->select('book_read_log.*', 'book_pool.book_name')
            ->orderBy('book_read_log.createtime', 'desc')
            ->where(array(
            'uid'=>$uid,
        ))->skip($start)->take($pagesize)->get();
        if($request->isMethod('post')){
            $html='';
            foreach($list as $key=>$val){
                $html.='<div class="weui_cell"  id="li_'.$val->id.'">';
                 $html.='       <div class="weui_cell_bd weui_cell_primary">';
                 $html.='           <p>';
                  $html.='              <a href="'.url('read/'.$val->book_id.'/'.$val->chapter_order).'">';
                 $html.='                   '.$val->book_name.' <span style="font-size:14px">第'.$val->chapter_order.'章</span>';
                  $html.='              </a>';
                  $html.='          </p>';
                   $html.='     </div>';
                  $html.='      <div class="weui_cell_ft">';
                   $html.='         <a href="javascript:;" onclick="del_readlog(this)" style="vertical-align: middle" data-log_id="'.$val->id.'" class="weui_btn weui_btn_mini weui_btn_warn">删除</a>';
                   $html.='     </div>';
                   $html.=' </div>';
            }
            echo $html;exit;
        }
        $tpl_dara=array(
            'list'=>$list,
        );
        return view('user_center.readlog',$tpl_dara);
    }

    public function del_readlog(Request $request){
        $id=$request->input('log_id');
        DB::table('book_read_log')->where('id',$id)->delete();
        return array(
            'code'=>1,
            'msg'=>'删除成功哦',
            'data'=>array(),
        );
    }

    public function del_bookshelf(Request $request){
        $id=$request->input('id');
        DB::table('bookshelf')->where('id',$id)->delete();
        return array(
            'code'=>1,
            'msg'=>'删除成功哦',
            'data'=>array(),
        );
    }

    public function book_coin_log(Request $request){
        $uid=session()->get('user.uid');
        $page=$request->input('page',1);
        $pagesize=$request->input('pagesize',10);
        $start=($page-1)*$pagesize;
        $list=DB::table("book_coin_log")
            ->join('book_pool', 'book_coin_log.book_id', '=', 'book_pool.id')
            ->select('book_coin_log.*', 'book_pool.book_name')
            ->orderBy('book_coin_log.createtime', 'desc')
            ->where(array(
                'uid'=>$uid,
            ))->skip($start)->take($pagesize)->get();
        if($request->isMethod('post')){
            $html='';
            foreach($list as $key=>$val){
                $html.='<div class="consume-item">';
                $html.='            <div>';
                $html.='                <span>消费书币: <span class="paid-welth">'.$val->price.'</span></span>';
                 $html.='               <span class="paid-date">'.date('Y-m-d H:i:s',$val->createtime).'</span>';
                $html.='            </div>';
                 $html.='           <div>';
                 $html.='               <span class="field">小说: </span>';
                  $html.='              <span>'.$val->book_name.'</span>';
                  $html.='          </div>';
                  $html.='          <div>';
                  $html.='              <span class="field">章节: </span>';
                  $html.='              <span>'.$val->chapter_order.'</span>';
                  $html.='          </div>';
                  $html.='      </div>';
            }
            echo $html;exit;
        }
        $tpl_dara=array(
            'list'=>$list,
        );
        return view('user_center.book_coin_log',$tpl_dara);
    }

    public function bookshelf_list(Request $request){
        $uid=session()->get('user.uid');
        $page=$request->input('page',1);
        $pagesize=$request->input('pagesize',10);
        $start=($page-1)*$pagesize;
        $list=DB::table("bookshelf")
            ->select('bookshelf.*')
            ->orderBy('bookshelf.addtime', 'desc')
            ->where(array(
                'uid'=>$uid,
            ))->skip($start)->take($pagesize)->get();
        if($request->isMethod('post')){
            $html='';
            foreach($list as $key=>$val){

                $html.='<div class="weui_cell">
                        <div class="weui_cell_bd weui_cell_primary">
                            <p>
                                <a href="'.url('read/'.$val->id.'/'.$val->chapter_order).'">第'.$val->chapter_order.'章 '.get_chapter_order_title($val->book_id,$val->chapter_order).'</a>
                            </p>
                        </div>
                        <div class="weui_cell_ft">
                            <a data-toggle="delete-bookmark" data-id="13310129" class="weui_btn weui_btn_mini weui_btn_warn">删除</a>
                        </div>
                    </div>';
            }
            echo $html;exit;
        }
        $tpl_dara=array(
            'list'=>$list,
        );
        return view('user_center.bookshelf_list',$tpl_dara);
    }
}