@extends('common.base')
@section('title'){{ $deal->book_name  }}@stop
@section('content')
<link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
<link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
<div class="container" style="padding-bottom:0">
    <div class="novel-detail">
        <div class="bd">
            <div class="novel-intro">
                <div class="novel-cover">
                    <img src="{{asset('static/'.$deal->cover_img)}}" width="105">
                </div>
                <div class="novel-info">
                    <h1 class="novel-title text-primary">{{ $deal->book_name  }}</h1>
                    <div class="novel-meta">
                        字数: {{ $deal->numofchars  }}字<br/>
                        @if(empty($deal->progress))
                        状态: 连载中
                        @else
                        状态: 完本
                        @endif
                    </div>
                </div>
            </div>
            <div class="novel-summary block">
                <h6><i class="iconfont icon-introduce"></i><span>简介</span></h6>
                {{ $deal->intro  }}</div>

            <div class="weui_btn_area">
                <a class="weui_btn weui_btn_primary" href="{{ url('read/'.$deal->id.'/1') }}">开始阅读</a>
            </div>

                <div class="novel-catalog block">
                    <div class="catalog-header">
                        <h6><i class="iconfont icon-book"></i><span>目录</span></h6>
                    </div>
                    <div class="weui_cells">
                        @foreach($book_chapter_list as $val)
                        <a class="weui_cell" href="{{ url('read/'.$deal->id.'/'.$val->chapter_order) }}">{{ $val->title }}
                        </a>
                        @endforeach
                    </div>
                    <div class="catalog-footer">
                        <a class="btn-see-more" href="{{ url('chapter_list/'.$deal->id) }}">全部目录</a>
                    </div>
                </div>
                </div>
            </div>
        </div>
<style>
    .father {
        width: 100%;
    }
    .daughter {
        position: relative;
        width: 100%;
        height: 0;
        padding-top: 100%;
        background: #f1f1f1;
    }
    .give_reward .promote .promote-user .user-img {
        width: 84px;
        max-width: 80%;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        display: inline-block;
    }
    .give_reward .promote .promote-user {
        width: 10%;
        float: left;
        text-align: center;
        margin-bottom: 10px;
    }
    .give_reward .promote {
        overflow: hidden;
        margin-bottom: 10px;
        padding: 10px 15px;
    }
    .give_reward .promote .promote-user .user-img img {
        width: 100%;
        display: block;
    }
    .daughter img {
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0px;
        top: 0px;
    }
</style>
@if(!empty($user_read_list))
<div class="give_reward">
    <span>他们也在看这本书</span>
    <div class="promote">
        @foreach($user_read_list as $val)
        <div class="promote-user">
            <a title="{{ $val->username }}" href="javascript:;" class="user-img">
                <div class="father">
                    <div class="daughter">
                        <img class="lazyload fadeIn" src="{{ $val->avatar }}" alt="{{ $val->username }}" height="50px" width="50px" />
                    </div>
                </div></a>
        </div>
        @endforeach
    </div>
</div>
<!--WAP版-->
<div id="SOHUCS" sid="cytBhXrcl" ></div>
<script id="changyan_mobile_js" charset="utf-8" type="text/javascript"
        src="https://changyan.sohu.com/upload/mobile/wap-js/changyan_mobile.js?client_id=cytBhXrcl&conf=prod_b8064a6729181a2b9d01676c116b667d">
</script>
@endif
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
    $(function(){
        @if(!empty($sign_status))
            $.alert("每日访问，赠送50书币哦", "签到成功");
        @endif
    })
</script>
<script>
    var wxShare = {
        wxTitle : '{{ $deal->book_name  }}',
        wxDesc : ' {{ str_limit(DeleteHtml($deal->intro), $limit = 100, $end = '...')}}',
        imgUrl : '{{asset('static/'.$deal->cover_img)}}',
        linkUrl : "{{ url('deal/'.$deal->id) }}"
    };
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '{{ $signPackage["appId"] }}',
        timestamp: {{ $signPackage["timestamp"] }},
        nonceStr: '{{ $signPackage["nonceStr"] }}',
        signature: '{{ $signPackage["signature"] }}',
        jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function(){
        wx.onMenuShareTimeline({
            title:wxShare.wxTitle, // 分享标题
            link:wxShare.linkUrl, // 分享链接
            imgUrl:wxShare.imgUrl, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareAppMessage({
            title:wxShare.wxTitle, // 分享标题
            desc:wxShare.wxDesc, // 分享描述
            link:wxShare.linkUrl,// 分享链接
            imgUrl:wxShare.imgUrl, // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
    wx.error(function(res){
        alert('网络出错');
    });
</script>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@endsection