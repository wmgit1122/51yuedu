@extends('common.base')
@section('title')首页@stop
@section('content')
<div class="container">
    <div class="cell">
        <div class="hd">
            <h1 class="page_title">
                {{ $deal->book_name }}
            </h1>
        </div>
        <div class="bd">
            <div class="weui_cells weui_cells_access" id="catalog-container">
                <div id="catalog-entries">
                    @foreach($book_chapter_list as $val)
                    <a class="weui_cell" href="{{ url('read/'.$deal->id.'/'.$val->chapter_order) }}" style="">
                            <div class="weui_cell_bd weui_cell_primary">
                            <p>{{ $val->title }}
                                <span style="color:#E72353;">
                                 @if ($val->ischarge == 1)
                                                @if(!empty($is_free))
                                                    限免
                                                @else
                                                    <i class="iconfont icon-diamond" style="vertical-align:middle"></i>
                                                @endif
                                @endif
                                </span>
                            </p>

                            </div>
                    </a>
                    @endforeach
            </div>
        </div>
    </div>
</div>
@endsection