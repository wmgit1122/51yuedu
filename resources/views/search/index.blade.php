@extends('common.base')
@section('title')首页@stop
@section('content')
<div class="page-search">
    <form action="{{ url('search') }}" method="post" id="form1">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="weui_search_bar">
            <div class="weui_search_outer">
                <div class="weui_search_inner">
                    <i class="weui_icon_search"></i>
                    <input class="weui_search_input" placeholder="请输入书名" required="" name="title" type="search">
                    <a href="javascript:" class="weui_icon_clear"></a>
                </div>
            </div>
            <a href="javascript:" onclick="$('#form1').submit();" class="btn-search">搜索</a>
        </div>
    </form>

    <div class="hot-panel">
        <p>热门搜索:</p>

        <div class="block">
            <ul>
                @foreach($search_hot as $val)
                <li class="hot">
                    <a href="{{ url('deal/'.$val->id) }}">{{ $val->book_name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="block block-novel-list">
        <h4 class="block-heading"><span class="total-of-results"></span></h4>
        <div class="block-body novel-list novels"></div>
        <p class="no-data">查无数据，换个条件试试看吧</p>
        <div class="weui-infinite-scroll">
            <div class="infinite-preloader"></div>
        </div>
    </div>

    <div class="block block-recommend-list">
        @if($method==1)
        <h4 class="block-heading">精彩推荐</h4>
        @else
        <h4 class="block-heading">搜索结果</h4>
        @endif
        <div class="block-body novel-list recommends">
            @foreach($list as $val)
            <div class="novel-item">
                <div class="novel-image">
                    <a href="{{ url('deal/'.$val->id) }}">
                        <img src="{{asset('static/'.$val->cover_img)}}">
                    </a>
                </div>
                <div class="novel-info">
                    <a href="{{ url('deal/'.$val->id) }}" class="novel-title">{{ $val->book_name }}</a>

                    <div>
                        <span class="novel-status novel-status-ongoing">
                            @if ($val->progress == 0)
                                连载中
                                @else
                                完结
                                @endif
                        </span>
                    </div>

                    <div class="novel-summary">
                        <a href="{{ url('deal/'.$val->id) }}">
                            {{ str_limit($val->intro , $limit = 100, $end = '...')}}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection