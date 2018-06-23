@extends('common.base')
@section('title')首页@stop
@section('content')
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
    @if(!empty($last_read_chapter))
    <div id="last-read-panel" style="padding:10px 10px;">
        <a  href="{{ url('read/'.$last_read_chapter->book_id.'/'.$last_read_chapter->chapter_order) }}" class="last-read-link" style="color:#10aeff;font-size:16px;">
            <i class="iconfont icon-history"></i> 上次阅读:
            <span class="last-read-text">
                《{{ $last_read_chapter->book_name }}》第{{ $last_read_chapter->chapter_order }}章
            </span>
        </a>
    </div>
    @endif

    <script src="{{asset('static/js/app.js')}}"></script>
    <script src="{{asset('static/js/vendor.js')}}"></script>
    <div id="top-novels-carousel" class="owl-carousel novel-carousel clearfix">

        @foreach($index_advert_list as $val)
        <a href="{{ url('deal/'.$val->id) }}">
            <div class="novel-carousel-item">
                <div class="novel-image">
                    <img src="{{asset('static/'.$val->cover_img)}}" />
                </div>
                <div class="novel-info">
                    <h3>{{ $val->book_name }}</h3>
                    <div class="novel-summary">
                        {{ str_limit($val->intro , $limit = 132, $end = '...') }}&hellip;                    </div>
                </div>
            </div>
        </a>
        @endforeach

    </div>

    <script>
        $('#top-novels-carousel').owlCarousel({
            items: 1,
            autoplay: true,
            autoplayTimeout: 3000,
            dots: true
        });
    </script>

    
    <div class="block ">
        <h3 class="block-heading"><i class="iconfont icon-star"></i> <span>主编力荐</span></h3>
        <div class="block-body">
            <div class="thumb-novel-list">
                @foreach($recommend_list as $key=> $val)
                    @if($key>2)
                        @break
                    @endif
                    <div class="novel-item">
                        <div class="novel-image">
                            <a href="{{ url('deal/'.$val->id) }}">
                                <img  class="lazy" data-original="{{asset('static/'.$val->cover_img)}}" src="{{asset('static/images/def_img.jpg')}}">
                            </a>
                        </div>
                        <div class="novel-name">
                            <a href="{{ url('deal/'.$val->id) }}">
                                {{ $val->book_name }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <ul class="text-novel-list">
                @foreach($recommend_list as $key=> $val)
                    @if($key<=2)
                        @continue
                    @endif
                    <li>
                        <a href="{{ url('deal/'.$val->id) }}">
                            {{ $val->book_name }}  </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @foreach($indexf as $key=>$val)
        @if($val->channel_id==2)
            <div class="block block-pink">
                @else
                    <div class="block block-blue">
                        @endif

        <h3 class="block-heading">
            <i class="iconfont icon-female"></i>
            <span>
                        @if($val->channel_id==2)
                            女生
                         @else
                          男生
                        @endif
            </span>
            <span class="block-subtitle">{{ $val->name  }}</span>
        </h3>
        <div class="block-body">

            <div class="novel-list">
                @foreach($val->book_list as $k=>$v)
                    @if($k > 2)
                        @break
                    @endif
                    <div class="novel-item" style="border-bottom:0">
                    <div class="novel-image">
                        <a href="{{ url('deal/'.$v->id) }}">
                            <img  class="lazy" data-original="{{asset('static/'.$v->cover_img)}}" src="https://cdn.zhangdu520.com/s/web/img/def_img.jpg">
                        </a>
                    </div>
                    <div class="novel-info">
                        <a class="novel-title" href="{{ url('deal/'.$v->id) }}">
                            {{ $v->book_name }}
                        </a>
                        <div class="novel-summary" style="font-size:14px">
                            {{ str_limit($v->intro , $limit = 116, $end = '...') }}
                        </div>
                    </div>
                    </div>
                @endforeach
            </div>
            <ul class="text-novel-list">
                @foreach($val->book_list as $kk=>$vv)
                    @if($kk>2)
                <li>
                    <a href="{{ url('deal/'.$vv->id) }}">
                        {{ $vv->book_name }}
                    </a>
                </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endforeach
@if(!empty($like_book_list))
    <div class="block ">
        <h3 class="block-heading"> <i class="iconfont icon-star"></i> <span>猜你喜欢</span><span id="change" style="float: right; font-size: 15px">换一批</span></h3>
        <div class="block-body">
            <div class="thumb-novel-list" id="change_like">
                @foreach($like_book_list as $key=> $val)
                <div class="novel-item">
                    <div class="novel-image">
                        <a href="{{ url('deal/'.$val->id) }}">
                            <img  class="lazy" width="136.98" height="163.77"   data-original="{{asset('static/'.$val->cover_img)}}" src="{{asset('static/images/def_img.jpg')}}">
                        </a>
                    </div>
                    <div class="novel-name">
                        <a href="{{ url('deal/'.$val->id) }}">
                            {{ $val->book_name }}
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
@if(!empty($free_list))
@if($free_list[0]->endtime-time() > 0)
<div class="block" id="time-limited-free-novel-list" style="" data-timer="{{ $free_list[0]->endtime-time() }}">
    <h3 class="block-heading"><i class="iconfont icon-clock"></i> 限时免费
        <span style="font-size:14px;float:right;margin-top:5px" class="free-period"></span>
    </h3>
    <div class="block-body">
        <div class="thumb-novel-list">
            @foreach($free_list as $key=>$val)
            <div class="novel-item">
                <div class="novel-image">
                    <a href="{{ url('deal/'.$val->id) }}"><img  class="lazy" data-original="{{asset('static/'.$val->cover_img)}}" src="https://cdn.zhangdu520.com/s/web/img/def_img.jpg"></a>
                    <i class="iconfont icon-time-limited-free"></i>
                </div>
                <div class="novel-name">
                    <a href="{{ url('deal/'.$val->id) }}">{{ $val->book_name }}</a>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
@endif
@endif
<script>
    $(function(){
        timer('time-limited-free-novel-list');//调用方法
        @if(!empty($sign_status))
            $.alert("每日访问，赠送50书币哦", "签到成功");
        @endif
    })
</script>

    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script>
        var wxShare = {
            wxTitle : '51阅吧',
            wxDesc : '51阅读，你想看的，你不想看，你有的，你没有的，都在这里哦，每天都要读书',
            imgUrl : '{{asset('static/images/logo.jpg')}}',
            linkUrl : "http://read.iyuebang.com/"
        };
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: '{{ $signPackage["appId"] }}',
            timestamp: {{ $signPackage["timestamp"] }},
            nonceStr: '{{ $signPackage["nonceStr"] }}',
            signature: '{{ $signPackage["signature"] }}',
            jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
        wx.ready(function(){
            wx.onMenuShareTimeline({
                title:wxShare.wxTitle, // 分享标题
                link:wxShare.linkUrl, // 分享链接
                imgUrl:wxShare.imgUrl, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareAppMessage({
                title:wxShare.wxTitle, // 分享标题
                desc:wxShare.wxDesc, // 分享描述
                link:wxShare.linkUrl,// 分享链接
                imgUrl:wxShare.imgUrl, // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
        wx.error(function(res){
            alert('网络出错');
        });
    </script>
@endsection
@section('footer')
<div style="margin-top:20px;padding:15px 0;text-align:center;border-top:#eee 1px solid;line-height:1.7em;color:#999;">
    <img style="width:48%;display:block;margin:20px auto;" src="//open.weixin.qq.com/qr/code?username=gh_da9e0739a442">
        长按识别上方二维码关注微信公众号<br/>
   <div style="text-align:center;font-size:13px">南京越邦信息科技有限公司版权所有<br/>
    苏ICP备14023933号-1<br/></div>
    
</div>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@if(!empty($free_list))
@if($free_list[0]->endtime-time() > 0)
<script type="text/javascript">
    var maxtime = $("#time-limited-free-novel-list").data('timer'); //一个小时，按秒计算，自己调整!
   function CountDown() {
           if (maxtime >= 0) {
               h = Math.floor(maxtime / 3600);
               minutes = Math.floor((maxtime-h*3600) / 60);
                  seconds = Math.floor(((maxtime-h*3600)-minutes*60) % 60);
                     msg = "还剩 "+h+" 时 "+minutes+" 分 "+seconds+" 秒";
                    $(".free-period").html(msg);
                         --maxtime;
                 } else{
                     $(".free-period").html('');
                }
        }
     timer = setInterval("CountDown()", 1000);
 </script>
@endif
@endif

<script src="{{asset('static/js/jquery.lazyload.js')}}"></script>
<script>
    $(function(){
        $("img.lazy").lazyload({
            placeholder: "{{asset('static/images/def_img.jpg')}}",
            effect: "fadeIn", // 载入使用何种效果
            threshold :50
        });
        $("#change").click(function(){
            $(this).html('加载中...')
            change_like();
        })
    });

    function load_img(){
        $("img.lazyy").lazyload({
            placeholder: "{{asset('static/images/def_img.jpg')}}",
            effect: "fadeIn", // 载入使用何种效果
            threshold :50
        });
    }


    function  change_like(){
        $.get('{{ url('change_like') }}',function(data){
            $("#change").html('换一批');
            if(data !=''){
                $("#change_like").empty();
                $("#change_like").append(data)
            }
        },'html')
    }
</script>
@endsection