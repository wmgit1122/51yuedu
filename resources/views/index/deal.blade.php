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

<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@endsection