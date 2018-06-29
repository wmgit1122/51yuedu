<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'wxpay',
        'wechat_notify',
        'ajax_category_list',
        'wechat',
        'user/readlog',
        'user/del_readlog',
        'user/book_coin_log',
        'user/bookshelf_list',
        'user/del_bookshelf',
        'get_code',
	    'login',
    ];
}
