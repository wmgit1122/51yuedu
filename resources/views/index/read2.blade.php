<!doctype html>
<html lang="zh-cn">
<head>
    <title>{{ $info->title }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="{{asset('static/css/mobile.css')}}">
    <link rel="stylesheet" href="{{asset('static/css/font_fsk2otdio7ds4i.css')}}" />
    <link rel="stylesheet" href="{{asset('static/css/front.css')}}" />
    <link rel="stylesheet" href="{{asset('static/css/vendor.css')}}" />
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
</head>
<body>
<article class="article-con">
    <h1 class="article-title">{{ $info->title }}</h1>
    <div class="two-col article-info">
        <div class="release-info">{{ $send_time }}&nbsp;发布&nbsp;|&nbsp;{{ $deal->numofchars }}字</div>
        <div class="article-set">
            <a href="javascript:void(0);" class="pattern"><i class="icon moon-icon"></i></a>
            <a href="javascript:void(0);" class="a-minus">A-</a>
            <a href="javascript:void(0);" class="a-plus">A+</a>

        </div>
    </div>
    <div class="betweenline"></div>
    <div class="article-bd">
        {!! $content !!}
    </div>
    @if (empty($isadd_bookshelf))
    <div class="reward-link"><a href="javascript:;" onclick="add_bookshelf({{ $deal->id }},{{ $chapter_order }})"><span class="circle"><i class="icon gold-icon"></i></span>加入书签</a></div>
    @endif
    <div class="chapter-page">
        @if ($has_last == 1)
            <a href="{{ url('read/'.$deal->id.'/'.($chapter_order-1)) }}" class="btn disable">上一章</a>
        @else
            <a href="javascript:;" class="btn disable">上一章</a>
        @endif
        @if ($has_next == 1)
            <a href="{{ url('read/'.$deal->id.'/'.($chapter_order+1)) }}" class="btn">下一章</a>
        @else
            <a href="javascript:;" class="btn">亲爱的没有了哦</a>
        @endif

    </div>

</article>

<script type="text/javascript" src="{{asset('static/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{asset('static/js/mobile.js')}}"></script>
<div class="sidespen">
    <a href="/" class="home"><i class="icon home-icon"></i></a>
    <a href="{{ url('chapter_list/'.$deal->id) }}" class="catalog"><i class="icon catalog-icon"></i></a>
    <a href="/user" class="ucenter"><i class="icon ucenter-icon"></i></a>
    <i class="icon close-icon"></i>
</div>
<script>
    $(function(){
        @if(!empty($sign_status))
            $.alert("每日阅读，赠送50书币哦", "签到成功");
        @endif
    })
    /**
     * 添加书架
     */
    function add_bookshelf(book_id,chapter_order){
        var post_data={
            'book_id':book_id,
            'chapter_order':chapter_order
        }
        $.ajax({
                type: 'POST',
                url: '{{ url('add_bookshelf') }}',
                data: post_data,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.code == 1) {
                        $.toast(data.msg);
                        $(".reward-link").remove();
                    } else {
                        $.toast(data.msg);
                    }
                }
            });
    }
</script>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
</body>
</html>
