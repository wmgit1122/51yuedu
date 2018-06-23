<?php
/**
 * Created by PhpStorm.
 * User: 369
 * Date: 2017/11/7
 * Time: 18:26
 */
namespace  App\Http\Middleware;

class Activity
{
    //前置
    public function handle($request,\Closure $next)
    {

        if(time() < strtotime('2017-11-05')){
            return redirect('activity0');
        }
        return $next($request);
    }

    //后置
//    public function handle($request,\Closure $next)
//    {
//        $response= $next($request);
//    }
}