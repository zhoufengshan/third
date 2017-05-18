<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/home/wwwroot/default/third/public/../application/index/view/index/order.html";i:1495085961;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单</title>
    <script type='text/javascript' src='http://cdn.staticfile.org/jquery/2.1.1/jquery.min.js'></script>
    <script type="text/javascript" src="http://cdn.staticfile.org/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script src="https://cdn.bootcss.com/layer/3.0.1/layer.min.js"></script>
</head>
<body>
    <input type="text" value="" name="total_fee" class="money">
    <button name="sub" class="pay">支付</button>

    <div id="qrcode" style="display: none;">

    </div>
</body>
<script>
    $(function () {
        $('.pay').on('click',function () {
            var  total_fee = $('.money').val();
            $.ajax({
                url : 'http://54.249.1.236/index/index/pay',
                dataType : 'json',
                type : 'post',
                data : {total_fee:total_fee},
                success : function (data) {
                    if(data.status == 'success'){
                        layer.open({
                            type: 1,
                            title: false,
                            closeBtn: 0,
                            area: '150',
                            skin: 'layui-layer-nobg', //没有背景色
                            shadeClose: true,
                            content: $('#qrcode')
                        });
                        jQuery('#qrcode').qrcode({width: 150,height: 150,text: data.url});
                        out_trade_no = data.out_trade_no;
                        setInterval(function(){
                            $.ajax({
                                url : 'http://54.249.1.236/index/index/confirmOrder',
                                dataType : 'json',
                                type : 'post',
                                data : {out_trade_no:out_trade_no},
                                success : function(data){
                                    if(data.code == 1){
                                        window.location.href = 'http://54.249.1.236/index/index/chenggong?out_trade_no='+data.data.out_trade_no;
                                    }
                                }
                            })
                        },1000);
                    }else if(data.status == 'false'){
                        layer.msg(data.msg);
                    }
                }
            })
        })

    })
</script>
</html>