@extends('common.base')
@section('title')首页@stop
@section('content')
<div class="novel-list" id="novel-list">
    @foreach($list as $val)
    <div class="novel-item">
        <div class="novel-image">
            <a href="{{ url('deal/'.$val->id) }}"><img src="{{asset('static/'.$val->cover_img)}}" /></a>
            <i class="iconfont icon-time-limited-free"></i>
        </div>
        <div class="novel-info">
            <a href="{{ url('deal/'.$val->id) }}" class="novel-title">{{ $val->book_name }}</a>
            <div class="novel-meta">
                <span style="color:darkorange"><i class="iconfont icon-clock" style="vertical-align: middle"></i>
                    <span style="vertical-align:middle">
                        限时免费: {{ date('m月d日',$val->starttime) }} ~ {{ date('m-d',$val->endtime) }}
                    </span>
                </span>
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
@endsection