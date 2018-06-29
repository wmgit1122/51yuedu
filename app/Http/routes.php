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

use Illuminate\Http\Response;

Route::any('redis_set',['uses'=>'TestController@redis_set']);
Route::any('queue',['uses'=>'TestController@queue']);

Route::any('game',['uses'=>'GameController@index']);

Route::any('send_mail',['uses'=>'TestController@mail']);

//基础路由
Route::group(['middleware' => ['web']], function () {

    Route::any('/',['uses'=>'IndexController@index']);

    Route::any('login',['uses'=>'LoginController@index']);

    Route::any('get_code',['uses'=>'LoginController@get_code']);

    Route::any('deal/{id}',['uses'=>'IndexController@deal'])->where('id','[0-9]+');

    Route::any('chapter_list/{id}',['uses'=>'IndexController@chapter_list']);

    Route::any('read/{id}/{chapter_order}',['uses'=>'IndexController@read'])->where('id','[0-9]+');

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
Route::any('member/{id}',['uses'=>'MemberController@info'])
->where('id','[0-9]+');