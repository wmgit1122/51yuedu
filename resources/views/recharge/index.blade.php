@extends('common.base')
@section('title')充值@stop
<link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
<link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
@section('content')
    <div class="container">
        <div class="cell">
            <div class="hd" style="padding: 1em 0 .8em">
                <h1 class="page_title">充值</h1>
            </div>
            <div class="bd">
                <div style="margin:0 10px;font-size:16px">
                    您的余额: <span id="balance" data-book_coin="{{ $user_info->book_coin }}" style="color:darkorange">{{ $user_info->book_coin }}</span> 书币
                </div>
                <hr>
                @if(!empty($price))
                <div style="margin:0 10px;font-size:16px">
                  阅读本章节需要: <span style="color:darkorange">{{ $price }}</span> 书币
                </div>
                <hr>
                @endif
                <div style="margin:10px 10px;font-size:16px;">
                    选择充值金额 <span style="color:orangered;">(1元=100书币)</span>
                </div>

                <div id="products-grid" class="products-grid clearfix">

                    @foreach($list as $key =>$val)
                    <div class="product-cell" style="">
                        <div class="product-item @if($key==0) active @endif " data-book_coin="{{ $val->book_coin }}" data-id="{{ $val->id }}" data-price="{{ $val->price }}">
                            <div class="product-welth"><span class="value">{{ $val->price }}</span>元</div>
                            <div class="product-info">
                                {{ $val->desc }}
                            </div>

                        </div>
                    </div>
                    @endforeach

                </div>
                <p class="weui_btn_area" style="margin-top:20px">
                    <a id="btn-pay-confirm" href="javascript:;" class="weui_btn weui_btn_primary">确认充值</a>
                </p>

                <div style="color:;padding:20px;line-height:1.8em;">
                    <span style="color:darkorange">*</span> 书币属于虚拟商品, 充值后无法退款, 请理解<br>
                    <span style="color:darkorange">*</span> 充值后书币自动到账<br>
                    <span style="color:darkorange">*</span> QQ:928147447, 咨询时请提供ID  {{ session('user.uid') }}<br>
                    <span style="color:darkorange">*</span> 工作时间:周一到周六 8:30-12:00, 13:30-23:00

                </div>

            </div>
        </div>
        <input type="hidden" name="pid" value="{{ $list[0]->id }}" />
        <input type="hidden" name="price" value="{{ $list[0]->price }}" />
        <input type="hidden" name="book_coin" value="{{ $list[0]->book_coin }}" />
    </div>
    <script type="text/javascript">
        //调用微信JS api 支付
        //调用微信JS api 支付
        function jsApiCall()
        {
            var pid= $("input[name='pid']").val();
            var price= $("input[name='price']").val();
            if(pid=='' || price==''){
                $.alert('请选择套餐');
                return false;
            }
            $.post('/wxpay/',{pid:pid},function(data){
                WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',
                        data,
                        function(res){
                            WeixinJSBridge.log(res.err_msg);
                            if(res.err_msg == "get_brand_wcpay_request:ok"){
                                $.toast('支付成功');
                                var book_coin=$("input[name='book_coin']").val();
                                var total_coin=parseInt($("#balance").data('book_coin'))+parseInt(book_coin);
                                $("#balance").data('book_coin',total_coin)
                                $("#balance").html(total_coin);
                            }else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                                $.toast("用户取消支付!");
                            }else{
                                $.toast("支付失败!");
                            }
                            $("#btn-pay-confirm").text('确认支付');
                        }
                );
            },'json');
            pay_status=true;
        }

        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>

    <script>
        var pay_status=true;
        $(function(){
            $('#btn-pay-confirm').on('click',function(){
                if(!pay_status){
                    return false;
                }
                $(this).text('请等待正在支付...')
                pay_status=false;
                callpay();
            })
            $('.product-item').on('click',function(){
                $('.product-item').removeClass("active");
                $(this).addClass("active");
                $("input[name='pid']").val($(this).data('id'));
                $("input[name='price']").val($(this).data('price'));
                $("input[name='book_coin']").val($(this).data('book_coin'));
            });
        })

    </script>
    <script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@endsection