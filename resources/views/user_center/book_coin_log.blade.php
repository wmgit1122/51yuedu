@extends('common.base')
@section('title')首页@stop
@section('content')
    <style>
        .loading,.endP{text-align: center;padding: 5px 0 15px;color: #999;}
    </style>
<link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
<link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
    <div class="page-consume-history">
        <div class="block">
            <div class="weui_tab">
                <div class="weui_navbar">
                    <a class="weui_navbar_item weui_bar_item_on" href="https://c4240.818tu.com/profile/consume">
                        消费记录
                    </a>
                </div>
                <div class="weui_tab_bd">
                    <div class="block-body consume-list">

                        @foreach($list as $val)
                        <div class="consume-item">
                            <div>
                                <span>消费书币: <span class="paid-welth">{{ $val->price }}</span></span>
                                <span class="paid-date">{{ date('Y-m-d H:i:s',$val->createtime) }} </span>
                            </div>
                            <div>
                                <span class="field">小说: </span>
                                <span>{{ $val->book_name }}</span>
                            </div>
                            <div>
                                <span class="field">章节: </span>
                                <span>{{ $val->chapter_order }}</span>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('static/js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{asset('static/js/main.js')}}"></script>
    <script src="{{asset('static/js/lazyload.js')}}"></script>
    <script>
        var flag = false;
        var page=1;
        $(function () {
            //请求数据,参数data是发送给后台的数据
            function getData(sendData) {
                $('.consume-list').after('<div class="loading">努力加载中...</div>');
                page++;
                $.ajax({
                    async:'false',
                    type:'POST',
                    url:'{{ url('user/book_coin_log') }}',
                    data : {
                        'page': page,
                        'pagesize':10
                    },
                    dataType : 'html',
                    success : function(html){
                        $('.loading').remove();
                        if (html=='') {
                            flag=true;
                            $('.consume-list').after('<div class="endP">已经到最后了</div>');
                        } else {
                            $('.consume-list').append(html);
                            echoInit();
                            flag = false;
                        }
                    }
                })
            }

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
    </script>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@endsection