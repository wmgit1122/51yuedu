<?php
/**
 * Created by PhpStorm.
 * User: iSkuggy
 * Date: 2018/6/8
 * Time: 14:03
 */
function sum_coin_log($where){
 return DB::table('book_coin_log')->where($where)->sum('price');
}