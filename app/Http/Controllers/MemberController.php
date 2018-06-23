<?php
/**
 * Created by PhpStorm.
 * User: 369
 * Date: 2017/11/7
 * Time: 14:09
 */
namespace App\http\Controllers;

use App\Member;
use Illuminate\Support\Facades\DB;

class MemberController extends  Controller{
    public function info($id){
      return  Member::getMember();
//         return 'member-info-id-'.$id;
//            return view('member-info');
    }
}