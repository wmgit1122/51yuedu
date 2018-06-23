@extends('common.base')
@section('title')首页@stop
@section('content')
<div class="container" style="padding-bottom:0">
    <div class="article" id="article-main" style="overflow-y:auto">
        <div class="hd">
            <h1 class="page_title">{{ $info->title }}
            </h1>
        </div>
        <div class="bd">
            <div id="novel-meta" class="novel-meta clearfix" style="">
                <div class="novel-avatar">
                    <img id="novel-avatar-img" data-src="{{asset('static/'.$deal->cover_img)}}" src="{{asset('static/'.$deal->cover_img)}}">
                </div>
                <div class="novel-info">
                    <span class="novel-title">{{ $info->title }}</span>
                </div>
            </div>
            <article class="weui_article">
                <section>
                    {!! $content !!}
                </section>
            </article>
            <div style="margin-bottom:35px;margin-top:-20px;display:none" id="subscribe-panel">
                <a class="weui_btn weui_btn_primary btn-subscribe" style="width:40%" href="javascript:;">追 书</a>
            </div>
            <div class="article-foot-actions">
                @if ($has_last == 1)
                <a id="__n" class="weui_btn btn-prev-article" href="{{ url('read/'.$deal->id.'/'.($chapter_order-1)) }}" >上一章节</a>
                @endif
                <a href="{{ url('chapter_list/'.$deal->id) }}"><i id="menu" class="iconfont icon-bars"></i></a>
                @if ($has_next == 1)
                    <a id="__n" class="weui_btn btn-next-article" href="{{ url('read/'.$deal->id.'/'.($chapter_order+1)) }}" >下一章节</a>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection