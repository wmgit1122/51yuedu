@extends('common.base')
@section('title')首页@stop
@section('content')
    <link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.2/style/weui.min.css">
    <link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.0/css/jquery-weui.min.css">
    <script src="{{asset('static/js/app.js')}}"></script>
    <script src="{{asset('static/js/vendor.js')}}"></script>
    <div class="page-bind-phone">
        <div class="hd" style="padding: 1em 0 .8em">
            <h1 class="page_title">登 录</h1>
        </div>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell weui_vcode">
                <div class="weui_cell_hd">
                    <label class="weui_label">邮箱账号</label>
                </div>
                <div class="weui_cell_bd">
                    <input class="weui_input email"  maxlength="50" placeholder="请输入邮箱账号">
                </div>
                <div class="weui_cell_ft">
                    <button class="btn-vcode">获取验证码</button>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">验证码</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input code" type="tel" maxlength="6" placeholder="请输入验证码">
                </div>
            </div>
        </div>
        <p class="weui_btn_area" style="margin-top:20px">
            <a href="javascript:;" class="weui_btn weui_btn_primary btn-auth">登 录</a>
        </p>
    </div>

    <script>
        $(function () {
            var reg = /^1[34578]\d{9}$/;
            var refLinkId = null;
            var $el = $(this);
            function disabled(el, seconds) {
                if (seconds <= 0) {
                    el.text('获取验证码').prop('disabled', '');
                } else {
                    el.text('请等待' + seconds + '秒').prop('disabled', 'disabled');
                    _.delay(disabled, 1000, el, seconds - 1);
                }
            }

            $('.btn-vcode').on('click', function () {
                var $el = $(this);
                var email = $('.email').val();
                if($.trim(email)==''){
                    return $.toast('请输入邮箱地址', 'forbidden');
                }
                $.ajax({
                        url: '{{ url('get_code') }}',
                        method: 'POST',
                        data: {email:email},
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(function () {
                        $.toast('发送成功');
                        disabled($el, 60);
                    }, function (error) {

                    });
            });

            $('.btn-auth').on('click', function () {
                var data = {
                    code: _.trim($('.code').val()),
                    email: _.trim($('.email').val()),
                    referral_link_id: refLinkId
                };

                if (_.isEmpty(data.email)) {
                    return $.toast('请输入邮箱', 'forbidden');
                }

                if (_.isEmpty(data.code)) {
                    return $.toast('请输入验证码', 'forbidden');
                }

                $.ajax({
                            url: '/login',
                            method: 'POST',
                            dataType: 'JSON',
                            data: data,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(function (result) {
                           if(result.code==1){
                               $.toast('登录成功');
                               window.location.href='/';
                           }else{
                               return $.toast('验证码错误', 'forbidden');
                           }
                        }, function (error) {

                        });
            });

            $('.phone').focus();
        });
    </script>
<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/jquery-weui/1.2.0/js/jquery-weui.min.js"></script>
@endsection