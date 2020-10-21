<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>合同模板</title>
    <script type="text/javascript" charset="utf-8" src="/assets/f46b666c/jquery.js"></script>
    <script type="text/javascript" charset="utf-8" src="https://webchat3.yunhetong.com/api_page/api/m/yht.js"></script>
    <style>
        .container{
            width:90%;
            margin:auto;
            text-align: center;
        }
        .logo{
            width:30%;
            margin-top:20%;
            margin-bottom:4%;
        }
        .title{
            margin-bottom:25%;
        }
        .first_btn,.last_btn{
            width:93%;
            height:50px;
            line-height:50px;
            color:#fff;
            border-radius:15px;
            text-align:center;
            margin:0 auto 80px;
        }
        .first_btn{
            background:#ff6d00;
        }
        .last_btn{
            background:#005aff;
        }

    </style>
    <script>
        var appid = "2020042717015000008";
        var contractId = '2009151635537093';
        // alert("contractId="+contractId);
        // alert("href="+location.href);
        var tokenUnableListener = function (obj){ //当 token 不合法时，SDK 会回调此方法
            var param = {
                'contractId' : contractId
            };
            $.ajax({
                type: 'get',
                url:'/contracts/token',  //第三方服务器获取 token 的 URL，云合同 SDK 无法提供
                cache: false,
                dataType: 'json',
                data:param,  //第三方获取 token 需要的参数
                beforeSend:function (xhr){
                    // xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                },
                success: function(data,textStatus,request){
                    // alert(data.data);
                    location.href = "https://webchat3.yunhetong.com/api_page/app/contract_sign_m.html?contractId="+contractId+"&token="+data.token;
                    // YHT.setToken(data.data);  //重新设置 token，从请求头获取 token
                    // YHT.do(obj); //调用此方法，会继续执行上次未完成的操作

                },
                error: function (data) {
                    alert(data);
                }
            });
        }


        function _sign() {
            var param = {
                'contractId' : contractId
            };
            $.ajax({
                type: 'GET',
                url: '/contracts/token',  //第三方服务器获取 token 的 URL，云合同 SDK 无法提供
                cache: false,
                dataType: 'json',
                data:param,  //第三方获取 token 需要的参数
                beforeSend:function (xhr){
                    // xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                },
                success: function(data,textStatus,request){
                    console.log(data.token);
                    location.href = "https://webchat3.yunhetong.com/api_page/app/contract_sign_m.html?contractId="+contractId+"&token="+data.token;
                    // YHT.setToken(data.data);  //重新设置 token，从请求头获取 token
                    // YHT.do(obj); //调用此方法，会继续执行上次未完成的操作

                },
                error: function (data) {
                    console.log(data);
                }
            });
            // YHT.init(appid, tokenUnableListener);
            // YHT.signContract(
            //     function successFun(url) {
            //         location.replace(url);
            //     },
            //     function failFun(data) {
            //         console.log(data);
            //     },
            //     contractId
            // );

        }

        // 前置绘制签名方法
        function dragSign() {
            YHT.init(contractId, tokenUnableListener);
            YHT.dragSignF(
                function successFun(url) {
                    location.href = url;
                },
                function failFun(data) {
                    console.log(data);
                }
            );

        }

        //运行
        //_sign();

    </script>
</head>

<body>
<div class="container">
    <img src="../images/photo.png" alt="" class="logo">
    <div class="title">合同在线签章</div>
    <div>
        <div onclick="_sign()" class="last_btn">签署合同</div>
    </div>
</div>
</body>
</html>