@extends('common.base')
@section('title')首页@stop
@section('content')
    <style>
        .loading,.endP{text-align: center;padding: 5px 0 15px;color: #999;}
    </style>
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
    <div class="container">
        <div class="cell">
            <div class="hd">
                <h1 class="page_title">书签</h1>
            </div>

            <div class="bd">

                <div class="weui_cells" id="readlogs-container">
                    @foreach($list as $val)
                    <div class="weui_cell" id="li_{{ $val->id  }}">
                        <div class="weui_cell_bd weui_cell_primary">
                            <p>
                                <a href="{{ url('read/'.$val->book_id.'/'.$val->chapter_order) }}">第{{ $val->chapter_order }}章 {{ get_chapter_order_title($val->book_id,$val->chapter_order)  }}</a>
                            </p>
                        </div>
                        <div class="weui_cell_ft">
                            <a data-toggle="delete-bookmark"  onclick="del_bookshelf(this)"  data-id="{{ $val->id }}" class="weui_btn weui_btn_mini weui_btn_warn">删除</a>
                        </div>
                    </div>
                    @endforeach

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
                page++;
                $.ajax({
                    async:'false',
                    type:'POST',
                    url:'{{ url('user/bookshelf_list') }}',
                    data : {
                        'page': page,
                        'pagesize':10
                    },
                    dataType : 'html',
                    success : function(html){
                        $('.loading').remove();
                        if (html=='') {
                            $('#readlogs-container').after('<div class="endP">已经到最后了</div>');
                            flag=true;
                        } else {
                            $('#readlogs-container').append(html);
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
                        $('#readlogs-container').after('<div class="loading">努力加载中...</div>');
                        getData();
                    }
                })
            })

        })
        function  del_bookshelf(obj){
            $.confirm({
                title: '删除书签',
                text: '确任删除书签吗',
                onOK: function () {
                    //点击确认
                    var $button = $(obj);
                    var id = $button.data('id');
                    $.post('{{ url('user/del_bookshelf') }}',{id:id},function(data){
                        $.toast(data.msg);
                        $("#li_"+id).remove();
                    },'json')
                },
                onCancel: function () {
                }
            });
        }
    </script>
    <script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@endsection