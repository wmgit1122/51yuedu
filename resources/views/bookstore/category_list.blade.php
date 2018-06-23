@extends('common.base')
@section('title')首页@stop
@section('content')
    <script src="{{asset('static/js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{asset('static/js/main.js')}}"></script>
    <script src="{{asset('static/js/lazyload.js')}}"></script>
    <div class="novel-list" id="novel-list">
        <h4 class="block-heading">{{ $category_info->category_name }}</h4>
        @foreach($list as $val)
            <div class="novel-item">
                <div class="novel-image">
                    <a href="{{ url('deal/'.$val->id) }}"><img src="{{asset('static/'.$val->cover_img)}}" /></a>

                </div>
                <div class="novel-info">
                    <a href="{{ url('deal/'.$val->id) }}" class="novel-title">{{ $val->book_name }}</a>
                    <div class="novel-meta">
                    </div>
                    <div class="novel-summary">
                        <a href="{{ url('deal/'.$val->id) }}">
                            {{ str_limit($val->intro , $limit = 168, $end = '...') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
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
                    url:'{{ url('ajax_category_list') }}',
                    data : {
                        'category_id':{{ $category_id  }},
                        'page': page,
                        'pagesize':10
                    },
                    dataType : 'html',
                    success : function(html){
                        if (html=='') {
                            flag=true;
                        } else {
                            $('#novel-list').append(html);
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
@endsection