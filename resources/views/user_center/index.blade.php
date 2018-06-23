@extends('common.base')
@section('title')首页@stop
<link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
<link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
@section('content')
    <div class="page-profile">
        <div class="avatar_wrapper">
            <div class="avatar">
                <img src="{{ $user->avatar }}">
            </div>

            <div class="fast_title w100">
                <div class="profile_label mt0">
                    {{ $user->username }} </div>
                <div class="profile_label" style="margin-top:5px">
                    ID: {{ $user->uid }} 书币:{{ $user->book_coin }} </div>
            </div>
        </div>
        <div class="bd">
            <div class="weui_grids">
                <a href="/recharge/" class="weui_grid">
                    <div class="weui_grid_icon">
                        <i class="icon iconfont icon-diamond" aria-hidden="false"></i>
                    </div>
                    <p class="weui_grid_label">
                        充值
                    </p>
                </a>
                <a href="{{ url('user/readlog') }}" class="weui_grid">
                    <div class="weui_grid_icon">
                        <i class="icon iconfont icon-history" aria-hidden="false"></i>
                    </div>
                    <p class="weui_grid_label">
                        阅读历史
                    </p>
                </a>
                <a href="{{ url('user/book_coin_log') }}" class="weui_grid">
                    <div class="weui_grid_icon">
                        <i class="icon iconfont icon-consume" aria-hidden="false"></i>
                    </div>
                    <p class="weui_grid_label">
                        消费记录
                    </p>
                </a>
                <a href="javascript:;" onclick="$.alert('所有用户每天都会送50书币哦');" class="weui_grid">
                    <div class="weui_grid_icon">
                        <i class="icon iconfont icon-gift" aria-hidden="false"></i>
                    </div>
                    <p class="weui_grid_label">
                        送书币
                    </p>
                </a>
                <a href="{{ url('user/bookshelf_list') }}"  class="weui_grid">
                    <div class="weui_grid_icon">
                        <i class="icon iconfont icon-bookmark"></i>
                    </div>
                    <p class="weui_grid_label">
                        书签
                    </p>
                </a>
                <a href="{{ url('bookstore') }}"  class="weui_grid ">
                    <div class="weui_grid_icon">
                        <i class="icon iconfont icon-bookshelf" aria-hidden="false"></i>
                    </div>
                    <p class="weui_grid_label">
                        书库
                    </p>
                </a>
                <a href="javascript:;" onclick="alert('敬请期待')" class="weui_grid binding hidden">
                    <span class="mobile-binding-reward">送50书币</span>
                    <div class="weui_grid_icon">
                        <i class="icon iconfont icon-account" aria-hidden="true"></i>
                    </div>
                    <p class="weui_grid_label">
                        手机绑定
                    </p>
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@endsection