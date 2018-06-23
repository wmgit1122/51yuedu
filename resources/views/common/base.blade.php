<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>【51阅读】- @yield('title')</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body,html{-webkit-tap-highlight-color:transparent}body{overflow-x:hidden;background-color:#fbf9fe}.container{height:100%;-webkit-overflow-scrolling:touch}.container>div{background-color:#fbf9fe}.hd{padding:1.5em 0}.page_title{text-align:center;font-size:24px;color:#3cc51f;font-weight:400;margin:0 15%}.page_desc{text-align:center;color:#888;font-size:14px}.bd.spacing{padding:0 15px}.home{padding-bottom:30px}.button .page_title{color:#225fba}.button .bd{padding:0 15px}.button .button_sp_area{padding:10px 0;width:60%;margin:0 auto;text-align:justify;text-justify:distribute-all-lines;font-size:0}.button .button_sp_area:after{display:inline-block;width:100%;height:0;font-size:0;margin:0;padding:0;overflow:hidden;content:"."}.cell .page_title{color:#225fba}.cell .bd{padding-bottom:30px}.dialog .bd,.toast .bd{padding:120px 15px 0}.msg{background-color:#fff}.panel .bd{padding-bottom:20px}.article{background-color:#fff}.article .page_title{color:#de7c23}.icons{background-color:#fff;text-align:center}.icons .page_title{color:#3e24bd}.icons .bd{padding:30px 0;text-align:center}.icons .icon_sp_area{padding:10px 20px;text-align:left}.icons i{margin:0 5px 10px}.tabbar{height:100%}.search_show{display:none;margin-top:0;font-size:14px}.search_show .weui_cell_bd{padding:2px 0 2px 20px;color:#666}.icon{display:inline-block;width:28px;height:28px;vertical-align:middle}.icon_button{background:url(images/icon_nav_button.png) no-repeat;background-size:28px 28px}.icon_cell{background:url(images/icon_nav_cell.png) no-repeat;background-size:28px 28px}.icon_toast{background:url(images/icon_nav_toast.png) no-repeat;background-size:28px 28px}.icon_dialog{background:url(images/icon_nav_dialog.png) no-repeat;background-size:28px 28px}.icon_progress{background:url(images/icon_nav_progress.png) no-repeat;background-size:28px 28px}.icon_msg{background:url(images/icon_nav_msg.png) no-repeat;background-size:28px 28px}.icon_article{background:url(images/icon_nav_article.png) no-repeat;background-size:28px 28px}.icon_actionSheet{background:url(images/icon_nav_actionSheet.png) no-repeat;background-size:28px 28px}.icon_icons{background:url(images/icon_nav_icons.png) no-repeat;background-size:28px 28px}.icon_panel{background:url(images/icon_nav_panel.png) no-repeat;background-size:28px 28px}.icon_tab{background:url(images/icon_nav_tab.png) no-repeat;background-size:28px 28px}.icon_search_bar{background:url(images/icon_nav_search_bar.png) no-repeat;background-size:28px 28px}@-webkit-keyframes a{0%{-webkit-transform:translate3d(100%,0,0);transform:translate3d(100%,0,0);opacity:0}to{-webkit-transform:translateZ(0);transform:translateZ(0);opacity:1}}@keyframes a{0%{-webkit-transform:translate3d(100%,0,0);transform:translate3d(100%,0,0);opacity:0}to{-webkit-transform:translateZ(0);transform:translateZ(0);opacity:1}}@-webkit-keyframes b{0%{-webkit-transform:translateZ(0);transform:translateZ(0);opacity:1}to{-webkit-transform:translate3d(100%,0,0);transform:translate3d(100%,0,0);opacity:0}}@keyframes b{0%{-webkit-transform:translateZ(0);transform:translateZ(0);opacity:1}to{-webkit-transform:translate3d(100%,0,0);transform:translate3d(100%,0,0);opacity:0}}.enter,.leave{position:absolute;top:0;right:0;bottom:0;left:0;z-index:1}.enter{-webkit-animation:a .2s forwards;animation:a .2s forwards}.leave{-webkit-animation:b .25s forwards;animation:b .25s forwards}
    </style>
    <link rel="stylesheet" href="{{asset('static/css/font_fsk2otdio7ds4i.css')}}" />
    <link rel="stylesheet" href="{{asset('static/css/front.css')}}" />
    <link rel="stylesheet" href="{{asset('static/css/vendor.css')}}" />
    <script src="{{asset('static/js/jquery.min.js')}}"></script>
    @section('style')

    @show
</head>
<body class=" has-footer-nav">
@section('header')
<div class="header">
    <ul class="top">
        <li style="text-align:left;padding-left:10px"><a href="/"><i class="iconfont icon-home"></i> 首页</a></li>
        <li><a href="{{ url('bookstore') }}">书库</a></li>
        <li><a href="{{ url('search') }}">搜索</a></li>
        <li><a href="{{ url('free') }}">限免</a></li>
        <li><a href="/recharge/"><i class="iconfont icon-diamond"></i> 充值</a></li>
    </ul>
</div>
@show

@section('user-profile-bar')
<div id="user-profile-bar" class="user-profile-bar">
    <a href="{{ url('user') }}">
        <span class="headimg"><img style="width:24px;height:24px;vertical-align:middle;border-radius:18px" src="{{ session('user.avatar') }}"></span>
        <span class="nickname" style="vertical-align:middle">{{ session('user.username') }}</span>
        <span style="float:right;vertical-align: middle">个人中心</span>
    </a>
</div>
@show

@section('content')
@show

@section('footer')

@show
@section('javascript')
@show
</body>
</html>
