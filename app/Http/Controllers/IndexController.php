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
use Illuminate\Support\Facades\Redis;
use App\lib\Response;

class IndexController extends CommonController
{

    //首页
    public function index(){
        $uid=session()->get('user.uid');
        //最近阅读的图书
        $last_read_chapter=Redis::get('last_read_chapter');
        if(empty($last_read_chapter)){
            $last_read_chapter=DB::table("book_read_log")
                ->join('book_pool', 'book_read_log.book_id', '=', 'book_pool.id')
                ->select('book_pool.book_name','book_read_log.book_id','book_read_log.chapter_order')
                ->where('book_read_log.uid',session()->get('user.uid'))
                ->where('book_pool.shelf_status',1)
                ->orderBy('book_read_log.createtime', 'desc')
                ->first();
            Redis::setex('last_read_chapter', config('common.redis_timeout'), serialize($last_read_chapter));
        }else{
            $last_read_chapter=unserialize($last_read_chapter);
        }
        //自定义分享
        //微信自定义分享
//        require_once app_path().'/Tools/jssdk/jssdk.php';
//        $jssdk = new \JSSDK("wx793c4440ee8af003", "54807aceb14de129375cb3a050a9fa72");
//        $signPackage = $jssdk->GetSignPackage();

        $index_advert_list=Redis::get('index_advert_list');
        if(empty($index_advert_list)){
            $index_advert_list=DB::table("index_advert")
                ->join('book_pool', 'index_advert.book_id', '=', 'book_pool.id')
                ->where('book_pool.shelf_status',1)
                ->select('book_pool.*')
                ->get();
            Redis::setex('index_advert_list', config('common.redis_timeout'), serialize($index_advert_list));
        }else{
            $index_advert_list=unserialize($index_advert_list);
        }
        $list=Redis::get('list');
        if(empty($list)) {
            $list = BookPool::where('shelf_status', 1)
                ->orderBy('updatetime', 'desc')
                ->take(10)
                ->get();
            Redis::setex('list', config('common.redis_timeout'), serialize($list));
        }else{
            $list=unserialize($list);
        }
        $recommend_list=Redis::get('recommend_list');
        if(empty($recommend_list)){
            $recommend_list=RecommendBook::leftJoin('book_pool','book_pool.id','=','recommend_book.book_id')
                ->where('book_pool.shelf_status',1)
                ->select('book_pool.*')->get();
            Redis::setex('recommend_list', config('common.redis_timeout'), serialize($recommend_list));
        }else{
            $recommend_list=unserialize($recommend_list);
        }

        $like_book_list=get_like_books($uid);

        $indexf=Redis::get('indexf');
        if(empty($indexf)){
            $indexf=DB::table("book_indexf")
                ->select('book_indexf.*')
                ->get();
            foreach($indexf as $key=>$val){
                $indexf[$key]->book_list=DB::table("book_indexf_list")
                    ->select('book_pool.*')
                    ->join('book_pool', 'book_indexf_list.book_id', '=', 'book_pool.id')
                    ->where('book_indexf_list.fid',$val->id)
                    ->where('book_pool.shelf_status',1)
                    ->get();
            }
            Redis::setex('indexf', config('common.redis_timeout'), serialize($indexf));
        }else{
            $indexf=unserialize($indexf);
        }
        $time=time();

        $free_list=Redis::get('free_list');
        if(empty($free_list)){
            $free_list=DB::table("book_free_list")
                ->join('book_pool', 'book_free_list.book_id', '=', 'book_pool.id')
                ->join('book_free', 'book_free_list.ext_id', '=', 'book_free.id')
                ->select('book_pool.*','book_free.starttime','book_free.endtime')
                ->where('book_free.isdelete',0)
                ->where('book_free.starttime', '<', $time)
                ->where('book_free.endtime', '>', $time)
                ->where('book_pool.shelf_status', 1)
                ->take(6)
                ->get();
            Redis::setex('free_list', config('common.redis_timeout'), serialize($free_list));
        }else{
            $free_list=unserialize($free_list);
        }
        $sign_status=auto_sign(session()->get('user.openid'));
        $tpl_dara=array(
            'indexf'=>$indexf,
            'list'=>$list,
            'recommend_list'=>$recommend_list,
            'index_advert_list'=>$index_advert_list,
//            'signPackage'=>$signPackage,
            'last_read_chapter'=>$last_read_chapter,
            'free_list'=>$free_list,
            'sign_status'=>$sign_status,
            'like_book_list'=>$like_book_list,
        );
        return view('index.index',$tpl_dara);
    }

    public function deal($id){
        $redis_key='book_id_'.$id;
        if(empty(Redis::get($redis_key))){
            $deal=BookPool::where('id', $id)->first();
            Redis::setex($redis_key,config('common.redis_timeout'),serialize($deal));
        }else{
            $deal=unserialize(Redis::get($redis_key));
        }
        //微信自定义分享
//        require_once app_path().'/Tools/jssdk/jssdk.php';
//        $jssdk = new \JSSDK("wx793c4440ee8af003", "54807aceb14de129375cb3a050a9fa72");
//        $signPackage = $jssdk->GetSignPackage();
        //取前五章节
        $table_name='book_chapter_'.$deal['id']%20;
        $book_chapter_list=DB::table($table_name)->orderBy('chapter_id', 'asc')->where('book_id',$id)->take(5)->get();

        $sign_status=auto_sign(session()->get('user.openid'));

        //查询阅读过该书的
        $user_read_list=DB::table('book_read_log')
            ->select('user.*')
            ->join('user','book_read_log.uid','=','user.uid')
            ->where('book_id',$id)->groupBy('uid')->get();


        $tpl_dara=array(
            'deal'=>$deal,
            'book_chapter_list'=>$book_chapter_list,
//            'signPackage'=>$signPackage,
            'sign_status'=>$sign_status,
            'user_read_list'=>$user_read_list,
        );
        return view('index.deal',$tpl_dara);
    }


    public function chapter_list($id){
        $deal=get_book_deal($id);
        //取前五章节
        $table_name='book_chapter_'.$deal['id']%20;
        $book_chapter_list=DB::table($table_name)->orderBy('chapter_id', 'asc')->where('book_id',$id)->get();
        $is_free=is_free($id);
        $tpl_dara=array(
            'deal'=>$deal,
            'book_chapter_list'=>$book_chapter_list,
            'is_free'=>$is_free,
        );
        return view('index.chapter_list',$tpl_dara);
    }

    public function read($id,$chapter_order,Request $request){
        $uid=session()->get('user.uid')?session()->get('user.uid'):0;//0匿名用户
        $deal=get_book_deal($id);
        $table_name='book_chapter_'.$id%20;
        $info=DB::table($table_name)->where(
            array(
                'chapter_order'=>$chapter_order,
                'book_id'=>$id,
            )
        )->first();
        $chapter_id=$info->chapter_id;
        switch($deal->supplier_id){
            case 1:
                $content=DB::table('book_content_818')->where('book_id',$id)->where('chapter_id',$chapter_id)->first();
                break;
            case 2:
                $content=DB::table('book_content_baozou')->where('book_id',$id)->where('chapter_id',$chapter_id)->first();
                break;
        }


        //插入readlog
        add_read_log($uid,$id,$chapter_order);

        //判断是不是限免
        if(!is_free($id)){
            $flag=buy_chapter($uid,$id,$chapter_order);
            if($flag==false){
                //跳转到充值页面
                $need_price=get_chapter_order_price($id,$chapter_order);
                return redirect('recharge/'.$need_price);
            }
        }
        //检查是否有下一章节
        $end=DB::table($table_name)->where('book_id',$id)->orderBy('chapter_id', 'desc')->first();
        $start=DB::table($table_name)->where('book_id',$id)->orderBy('chapter_id', 'asc')->first();
        $has_next=0;
        $has_last=0;
        if($end->chapter_order > $chapter_order){
            $has_next=1;
        }
        if($chapter_order > $start->chapter_order ){
            $has_last=1;
        }
        $send_time=format_date($deal['updatetime']);


        $isadd_bookshelf=DB::table('bookshelf')->where(array(
            'book_id'=>$id,
            'chapter_order'=>$chapter_order,
            'uid'=>$uid,
        ))->first();

        $sign_status=auto_sign(session()->get('user.openid'));
        $tpl_dara=array(
            'isadd_bookshelf'=>$isadd_bookshelf,
            'has_next'=>$has_next,
            'has_last'=>$has_last,
            'deal'=>$deal,
            'info'=>$info,
            'content'=>$content->content,
            'chapter_order'=>$chapter_order,
            'send_time'=>$send_time,
            'sign_status'=>$sign_status,
        );
        return view('index.read2',$tpl_dara);
    }

    public function add_bookshelf(Request $request){
        $book_id=$request->input('book_id');
        $chapter_order=$request->input('chapter_order');
        $uid=session()->get('user.uid');
        if(empty($uid)){
            Response::show(0, '请先登录');
        }
        if(empty($book_id)){
            Response::show(0, '缺少参数(book_id)');
        }
        if(empty($chapter_order)){
            Response::show(0, '缺少参数(chapter_order)');
        }
        if(isadd_bookshelf($book_id,$chapter_order,$uid)){
            Response::show(1, '添加成功');
        }else{
            Response::show(0, '你已经添加过该书');
        }

    }


    public function change_like(){
        $uid=session()->get('user.uid');
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
        $html='';
        if($category_arr){
            $book_list=DB::table('book_pool')->where('shelf_status',1)->whereIn('category_id', $category_arr)->orderBy(DB::raw('RAND()'))->take(6)->get();
            foreach($book_list as $key=>$val){
                $html.='<div class="novel-item">
                    <div class="novel-image">
                        <a href="'.url('deal/'.$val->id).'">
                            <img  class="lazyy" width="136.98" height="173.77"  data-original="'.asset('static/'.$val->cover_img).'" src="'.asset('static/images/def_img.jpg').'">
                        </a>
                    </div>
                    <div class="novel-name">
                        <a href="'.url('deal/'.$val->id).'">
                           '.$val->book_name.'
                        </a>
                    </div>
                </div>';
            }

        }
        if($html){
            $html=$html.'<script>load_img();</script>';
        }
        echo $html;exit;

    }

}