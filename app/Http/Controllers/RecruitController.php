<?php
/**
 * Created by PhpStorm.
 * User: 369
 * Date: 2017/11/8
 * Time: 14:52
 */
namespace App\Http\Controllers;

use App\Goods;
use App\Member;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecruitController extends Controller
{
    public function index(){
        return view('recruit.index');
    }

    public function baoming(Request $request){
        if($request->method('post')){
            $data=$request->input('member');
            $Member = new Member();
            $data['ip']=$request->ip();
            $bool=$Member::create($data);
            if($bool){
                echo '<script>alert("报名成功");window.location.href="/recruit";</script>';exit;
            }
        }
    }

    public function upload(Request $request){
        if($request->isMethod('post')){
            $file=$request->file('file');
            //原文件名
            $originaName=$file->getClientOriginalName();
            //扩展名
            $ext=$file->getClientOriginalExtension();
            //临时绝对路径
            $realPath=$file->getRealPath();
            $filename=date('Y-m-d-H-i-s').'_'.uniqid().'.'.$ext;
            $res=$request->file('file')->move(public_path('uploads'), $filename);
            if(!empty($res)){
                return response()->json(['code' => '1', 'data' =>$res->getFilename(),'msg'=>'']);
            }
            // bool=Storage::disk('uploads')->put($filename,file_get_contents($realPath));
            //var_dump($bool);exit;

        }
    }
}