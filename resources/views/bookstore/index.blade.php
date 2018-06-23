@extends('common.base')
@section('title')书库@stop
@section('content')
<link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
<link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
<style>
    .pdlr10 {
        padding: 0 10px;
    }
    .unit {
        background-color: #fff;
        margin-bottom: 10px;
    }
    .sstag {
        padding: 10px 0;
    }
    .sstag a.active {
        border: 1px solid #E72353;
        color: #FFFFFF;
        background-color: #E72353;
    }
    .sstag a {
        font-size: 14px;
        background-color: #fff;
        border: 1px solid #ddd;
        color: #666;
        height: 30px;
        line-height: 30px;
        padding: 0 4px;
        margin: 0 5px 10px 0;
        display: inline-block;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
    }
    .unit {
        background-color: #fff;
        margin-bottom: 10px;
    }
    .tit, .tit2, .tit3 {
        height: 38px;
        line-height: 38px;
        width: 100%;
        padding: 0 10px;
    }
    .tit h1 {
        color: #ff286e;
    }
    .tit h1 {
        font-size: 16px;
        float: left;
    }
    .cgray {
        color: #aaa;
    }
    .frame li {
        position: relative;
        padding: 15px 10px;
        border-top: 1px solid #eee;
    }
    .frame .rt2 {
        padding-left: 0;
    }

    .frame h1 {
        height: 36px;
        line-height: 36px;
        font-size: 18px;
    }
    .hidden {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    h1, h2, h3, h4, h5, h6 {
        font-weight: normal;
    }
    .frame p {
        color: #787878;
    }
    .frame .rt p, .frame .rt2 p {
        overflow: hidden;
    }
    .frame p {
        color: #787878;
    }
    .loading,.endP{text-align: center;padding: 5px 0 15px;color: #999;}
</style>
<div class="unit pdlr10" style="margin-top:15px;">
    <div class="sstag" style="float: right;">
        <div class="classids" id="nv1">
            <span class="label">分类:</span>
            <a href="javascript:void(0);" class="active" onclick="select(0,this)" >不限</a>
            @foreach($channel as $key=>$val)
            <a href="javascript:void(0);" onclick="select({{ $val->category_id }},this)" >{{ $val->category_name }}</a>
            @endforeach
        </div>
        <div id="showorhidden" style="">
            <div class="finish">
                <span class="label">更新:</span>
                <a class="active" href="javascript:void(0);" onclick="set_progress(0,this)">不限</a>
                <a href="javascript:void(0);" onclick="set_progress(1,this)">完本</a>
                <a href="javascript:void(0);" onclick="set_progress(2,this)">连载</a>
            </div>
            <div class="finish">
                <span class="label">频道:</span>
                <a class="active" href="javascript:void(0);" onclick="set_channel_id(0,this)">不限</a>
                <a href="javascript:void(0);" onclick="set_channel_id(1,this)">男频</a>
                <a href="javascript:void(0);" onclick="set_channel_id(2,this)">女频</a>
            </div>
        </div>
        </div>
    </div>

</div>

<div id="loadmore" pagenum="4" totalnum="10676" totalpage="1068" keyword="" keywordtype="1" pclassids="0" classids="0" free="0" finish="0" charnum="0" updatetime="0" order="1"></div>
{{--<div class="tit"><h1 class="cgray" id="totalcount">共有10676条搜索结果</h1></div>--}}
<div class="novel-list" id="novel-list">
@foreach($list as $val)
    <div class="novel-item">
        <div class="novel-image">
            <a href="{{ url('deal/'.$val->id) }}"><img class="lazy"  width="100" height="135" data-original="{{asset('static/'.$val->cover_img)}}" src="{{asset('static/images/def_img.jpg')}}" /></a>

        </div>
        <div class="novel-info">
            <a href="{{ url('deal/'.$val->id) }}" class="novel-title">{{ $val->book_name }}</a>
            <div class="novel-meta">
            </div>
            <div class="novel-summary">
                <a href="{{ url('deal/'.$val->id) }}" style="font-size:14px">
                    {{ str_limit($val->intro , $limit = 116, $end = '...') }}
                </a>
            </div>
        </div>
    </div>
@endforeach
</div>
<input type="hidden" id="category_id" value="0">
<input type="hidden" id="channel_id" value="0">
<input type="hidden" id="progress" value="0">

<script src="{{asset('static/js/jquery-2.1.4.min.js')}}"></script>
<script src="{{asset('static/js/main.js')}}"></script>
<script src="{{asset('static/js/lazyload.js')}}"></script>
<script>
    var flag = false;
    var page=1;
    var category_id=0;
    $(function () {
        //请求数据,参数data是发送给后台的数据
        //图片懒加载
        echoInit();

        //上拉加载
        $(window).load(function () {
            $(window).on('scroll', function () {
                //滚动到底部，开始加载数据
                if($(window).scrollTop() >= $(document).height() - $(window).height() - 40){
                    if(flag) return;
                    flag = true;
                    //请求获取数据
                    getData();
                }
            })
        })
    })

    function getData() {
        $('#novel-list').after('<div class="loading">努力加载中...</div>');
        $('.endP').remove();
        page++;
        $.ajax({
            async:'false',
            type:'POST',
            url:'{{ url('ajax_category_list') }}',
            data : {
                'category_id':$("#category_id").val(),
                'channel_id':$("#channel_id").val(),
                'progress':$("#progress").val(),
                'page': page,
                'pagesize':10
            },
            dataType : 'html',
            success : function(html){
                $('.loading').remove();
                if (html=='') {
                    $('#novel-list').after('<div class="endP">已经到最后了</div>');
                    flag=true;
                } else {
                    $('#novel-list').append(html);
                    echoInit();
                    flag = false;
                }
            }
        })
    }

    function select(category_id,obj){
        $(obj).addClass('active')
        $(obj).siblings().removeClass('active');
        $("#category_id").val(category_id);
        $('#novel-list').html('');
        page=0;
        getData();
    }

    function set_progress(progress,obj){
        $(obj).addClass('active')
        $(obj).siblings().removeClass('active');
        $("#progress").val(progress);
        $('#novel-list').html('');
        page=0;
        getData();
    }

    function set_channel_id(channel_id,obj){
        $(obj).addClass('active')
        $(obj).siblings().removeClass('active');
        $("#channel_id").val(channel_id);
        $('#novel-list').html('');
        page=0;
        getData();
    }
</script>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
<script src="{{asset('static/js/jquery.lazyload.js')}}"></script>
<script>
    $(function(){
        $("img.lazy").lazyload({
            placeholder: "{{asset('static/images/def_img.jpg')}}",
            //effect: "fadeIn", // 载入使用何种效果
            threshold :50
        });
    });
    function load(_class){
        $("img."+_class+"").lazyload({
            placeholder: "{{asset('static/images/def_img.jpg')}}",
            //effect: "fadeIn", // 载入使用何种效果
            threshold :50
        });
    }
</script>
@endsection