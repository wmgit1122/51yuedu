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

class BookstoreController extends Controller
{

    public function index(Request $request){
        $category_id=$request->input('category_id','0');
        $uid =session()->get('user.uid');
        $user_info = get_user_info($uid);
        $channel=DB::table('book_category')->where(array('status'=>1))->orderBy('order','asc')->get();
        foreach($channel as $key=>$val){
            $channel[$key]->total=get_category_count($val->category_id);
        }
        $category_info=get_category_info($category_id);
        if(!empty($category_id)){
            $list=DB::table("book_pool")
                ->select('book_pool.*')
                ->where('book_pool.isdelete',0)
                ->where('book_pool.shelf_status','1')
                ->where('book_pool.category_id',$category_id)
                ->orderBy('id', 'desc')
                ->skip(0)->take(10)
                ->get();
        }else{
            $list=DB::table("book_pool")
                ->select('book_pool.*')
                ->where('book_pool.isdelete',0)
                ->where('book_pool.shelf_status','1')
                ->orderBy('id', 'desc')
                ->skip(0)->take(10)
                ->get();
        }
        $tpl_dara = array(
            'list' => $list,
            'user_info' => $user_info,
            'channel'=>$channel,
            'category_id'=>$category_id,
        );
        return view('bookstore.index',$tpl_dara);
    }

    public function category_list($category_id){
        //获取分类信息
        $category_info=get_category_info($category_id);
        $list=DB::table("book_pool")
            ->select('book_pool.*')
            ->where('book_pool.isdelete',0)
            ->where('book_pool.shelf_status','1')
            ->where('book_pool.category_id',$category_id)
            ->orderBy('id', 'desc')
            ->skip(0)->take(10)
            ->get();
        $tpl_dara = array(
            'list' => $list,
            'category_info'=>$category_info,
            'category_id'=>$category_id,
        );
        return view('bookstore.category_list',$tpl_dara);
    }

    public function ajax_category_list(Request $request){
        $category_id=$request->input('category_id');
        $progress=$request->input('progress');
        $channel_id=$request->input('channel_id');
        $where=array();
        if(!empty($category_id)){
            $where['book_pool.category_id']=$category_id;
        }
        if(!empty($channel_id)){
            $where['book_pool.channel_id']=$channel_id;
        }
        if(!empty($progress)){
            if($progress==1){
                $where['book_pool.progress']=1;
            }else{
                $where['book_pool.progress']=0;
            }
        }
        $page=$request->input('page',1);
        $pagesize=$request->input('pagesize',10);
        $start=($page-1)*$pagesize;
        $list=DB::table("book_pool")
            ->select('book_pool.*')
            ->where('book_pool.isdelete',0)
            ->where('book_pool.shelf_status','1')
            ->where($where)
            ->orderBy('id', 'desc')
            ->skip($start)->take($pagesize)
            ->get();
        $html='';
        if(!empty($list)){
            foreach($list as $key=>$val){
                $html.='    <div class="novel-item">';
                 $html.='       <div class="novel-image">';
                 $html.='           <a href="'.url('deal/'.$val->id).'"><img width="100" height="135" class="lazy_'.$page.'"  data-original="'.asset('static/'.$val->cover_img).'" src="'.asset('static/images/def_img.jpg').'" /></a>';

                 $html.='       </div>';
                  $html.='      <div class="novel-info">';
                  $html.='          <a href="'.url('deal/'.$val->id).'" class="novel-title">'.$val->book_name.'</a>';
                   $html.='         <div class="novel-meta">';
                   $html.='         </div>';
                   $html.='         <div class="novel-summary">';
                   $html.='             <a href="'.url('deal/'.$val->id).'">';
                    $html.='          '.str_limit($val->intro , $limit = 68, $end = '...').'';
                     $html.='           </a>';
                     $html.='       </div>';
                     $html.='   </div>';
                   $html.=' </div>';
                $html.='<script>load(\'lazy_'.$page.'\');</script>';
            }
        }
        echo $html;exit;
    }
}
