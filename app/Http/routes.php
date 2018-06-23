<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('user/profile', ['as' => 'profile', function () {
    $url = route('profile');
    echo $url;exit;
}]);
Route::any('game',['uses'=>'GameController@index']);

Route::any('send_mail',['uses'=>'TestController@mail']);

Route::any('queue',['uses'=>'TestController@queue']);

Route::any('redis_test',['uses'=>'TestController@redis_test']);

//基础路由
Route::group(['middleware' => ['web']], function () {

    Route::any('/',['uses'=>'IndexController@index']);

    Route::any('deal/{id}',['uses'=>'IndexController@deal']);

    Route::any('chapter_list/{id}',['uses'=>'IndexController@chapter_list']);

    Route::any('read/{id}/{chapter_order}',['uses'=>'IndexController@read']);

    Route::any('user',['uses'=>'UsercenterController@index']);

    Route::any('user/readlog',['uses'=>'UsercenterController@readlog']);

    Route::any('user/del_readlog',['uses'=>'UsercenterController@del_readlog']);

    Route::any('user/del_bookshelf',['uses'=>'UsercenterController@del_bookshelf']);

    Route::any('user/book_coin_log',['uses'=>'UsercenterController@book_coin_log']);

    Route::any('recharge/{price?}',['uses'=>'RechargeController@index']);

    Route::any('wxpay',['uses'=>'PayController@index']);

    Route::any('search',['uses'=>'SearchController@index']);

    Route::any('free',['uses'=>'FreeController@index']);

    Route::any('bookstore',['uses'=>'BookstoreController@index']);

    Route::any('category/{id}',['uses'=>'BookstoreController@category_list']);

    Route::any('ajax_category_list',['uses'=>'BookstoreController@ajax_category_list']);

    Route::any('add_bookshelf',['uses'=>'IndexController@add_bookshelf']);

    Route::any('user/bookshelf_list',['uses'=>'UsercenterController@bookshelf_list']);

    Route::any('wechat',['uses'=>'WechatController@index']);

    Route::any('wechat_setMenu',['uses'=>'WechatController@setMenu']);

    Route::any('change_like',['uses'=>'IndexController@change_like']);
});
Route::any('wechat_notify',['uses'=>'PayController@notify']);

Route::any('test',['uses'=>'TestController@index']);

Route::any('lists/{id}',['uses'=>'IndexController@lists'])->where(['id' => '[0-9]+']);

Route::any('recruit',['uses'=>'RecruitController@index']);

Route::any('recruit/upload',['uses'=>'RecruitController@upload']);

Route::any('recruit/baoming',['uses'=>'RecruitController@baoming']);

Route::any('error',['uses'=>'StudentController@error']);

Route::any('upload',['uses'=>'StudentController@upload']);

Route::any('cache1',['uses'=>'StudentController@cache1']);

Route::any('cache2',['uses'=>'StudentController@cache2']);

Route::group(['middleware' => ['web']], function () {
    //
    Route::any('session1', ['uses' => 'StudentController@session1']);
    Route::any('session2', [
        'as' => 'session2',
        'uses' => 'StudentController@session2'
    ]);

    Route::any('test11', ['uses' => 'StudentController@test11']);

    Route::any('response', ['uses' => 'StudentController@response']);

    Route::get('student/index', ['uses' => 'StudentController@index']);
    Route::any('student/create', ['uses' => 'StudentController@create']);
    Route::any('student/save', ['uses' => 'StudentController@save']);
    Route::any('student/update/{id}', ['uses' => 'StudentController@update']);
    Route::any('student/detail/{id}', ['uses' => 'StudentController@detail']);
    Route::any('student/delete/{id}', ['uses' => 'StudentController@delete']);
});




Route::any('test1',['uses'=>'StudentController@test1']);

Route::any('query1',['uses'=>'StudentController@query1']);

Route::any('query2',['uses'=>'StudentController@query2']);

Route::any('query3',['uses'=>'StudentController@query3']);

Route::any('query4',['uses'=>'StudentController@query4']);

Route::any('query5',['uses'=>'StudentController@query5']);

Route::any('orm1',['uses'=>'StudentController@orm1']);

Route::any('orm2',['uses'=>'StudentController@orm2']);

Route::any('orm3',['uses'=>'StudentController@orm3']);

Route::any('orm4',['uses'=>'StudentController@orm4']);

Route::any('section1',['uses'=>'StudentController@section1']);

Route::any('request1',['uses'=>'StudentController@request1']);

Route::any('response',['uses'=>'StudentController@response']);

Route::group(['middleware'=>['web']],function(){
    Route::any('session1',['uses'=>'StudentController@session1']);
    Route::any('session2',['uses'=>'StudentController@session2']);
});

//宣传页面
Route::any('activity0',['uses'=>'StudentController@activity0']);
//活动页面
Route::group(['middleware'=>['activity']],function(){
    Route::any('activity1',['uses'=>'StudentController@activity1']);
    Route::any('activity2',['uses'=>'StudentController@activity2']);
});







//Route::get('member/info','MemberController@info');



//Route::get('member/info',[
//    'uses'=>'MemberController@info',
//    'as'=>'memberinfo',
//]);
//Route::post('member/info',['uses'=>'MemberController@info']);
//Route::any('member/info',['uses'=>'MemberController@info']);


Route::any('member/{id}',['uses'=>'MemberController@info'])
->where('id','[0-9]+');


Route::get('basic1', function () {
    return 'hello world';
});

Route::post('basic2', function () {
    return 'basic2';
});

//多请求路由
Route::match(['get','post'],'multy1',function(){
    return 'multy1';
});

Route::any('multy2',function(){
    return 'multy2';
});

//路由参数
//Route::get('user/{id}',function($id){
//    return 'User-id-'.$id;
//});
//
//Route::get('user/{name?}',function($name=null){
//    return 'User-name-'.$name;
//});
//
//Route::get('user/{name?}',function($name='jack'){
//    return 'User-name-'.$name;
//})->where('name','[A-Za-z]+');
//
//Route::get('user/{name?}',function($name='jack'){
//    return 'User-name-'.$name;
//})->where('name','[A-Za-z]+');

//Route::get('user/{$id}/{name?}',function($id,$name='jack'){
//    return 'User-id-'.$id.'-name-'.$name;
//})->where(['id'=>'[0-9]+','name'=>'[A-Za-z]+']);

//路由别名
//Route::get('user/member-center',['as'=>'center',function(){
//   return route('center');
//}]);

//路由群组
Route::group(['prefix'=>'member'],function(){
    Route::get('user/member-center',['as'=>'center',function(){
        return route('center');
    }]);
    Route::any('multy2',function(){
        return 'member-multy2';
    });
});

//路由中输出识图
Route::get('view', function () {
    return view('welcome');
});