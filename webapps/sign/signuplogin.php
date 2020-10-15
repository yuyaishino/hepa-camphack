<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" type="image/png" href="www">
    <title>ヘパリーゼW×CAMP HACK　厳選キャンプグッズプレゼントキャンペーン</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta http-equiv="default-style" property="og:title" content="">
    <meta http-equiv="default-style" property="og:type" content="website">
    <meta http-equiv="default-style" property="og:url" content="">
    <meta http-equiv="default-style" property="og:image" content="">
    <meta http-equiv="default-style" property="og:site_name" content="">
    <meta http-equiv="default-style" property="og:description" content="">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="">
    <meta name="twitter:title" content="">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="ogp.jpg">
    <link rel="stylesheet" type="text/css" href="../../public/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="../../public/css/hexca.css" media="all">
    <link rel="stylesheet" type="text/css" href="../../public/css/adjust.css" media="all">
    <link rel="stylesheet" type="text/css" href="../../public/css/fixed_color.css" media="all">
    <link rel="stylesheet" type="text/css" href="../../public/css/loading.css" media="all">
    <link rel="stylesheet" type="text/css" href="../../public/css/font-awesome.css" media="all">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PQZV8LK');</script>
    <!-- End Google Tag Manager -->
    <!--[if lt IE 9]>
    <script src="/assets/common/js/html5shiv.min.js"></script><![endif]-->
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->
    <script src="../../public/jquery/jquery.min.js"></script>
    <script src="../../public/js/common.js"></script>

    <script type="text/javascript">

        function verify(){
            // 一、验证邮箱
            // 1. 是否全部是半角
            // 2. 字母是否都是小写字母
            // 3. 符号仅仅限于  .,-,_
            // 4. 第一个字母是英文
            let email = $("input[name='email']").val();
            if(isFullAngle(email) || isContainsUpperCase(email) || !isEmail(email) || !isContainsLowerCase(email.substring(0,1))){
                showError();
                return false;
            }


            // 二、 验证密码
            let password = $("input[name='password']").val();

            // 1. 是否全部是半角
            // 2.   5以上 、 21 以下
            if(isFullAngle(password) || password.length < 6 || password.length > 20 ){
                showError();
                return false;
            }
            return true;
        }

        function login(){
            if(verify()){
                let data =  $('form').serializeObject()
                data.id = getQueryVariable("id");
                $.ajax({
                    url:'../../application/sign/SignupLogin.php',
                    data:data,
                    dataType:'json',
                    type:'post',
                    success:function(result){
                        console.log(result);
                        if(result.status == 0){ //失败
                            $('.error').show();
                        } else {
                            //页面跳转
                            //window.location.href="signupcheck.php";
                            window.location.replace('../user/mypage.php')
                        }
                    },
                    error:function(h,e,n){
                        console.log(e);
                    }
                })
            }

            return false;
        }

        function showError(){
            $('.error').show();
        }

    </script>

</head>


<body id="top">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQZV8LK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="wrap">

<!--    <header>
        <div class="header-logo custom-header-bg">
            <div class="header-logo_area">
                <a href="#"><img src="../../public/img/sebun_logoN.png" alt=""></a>
            </div>
        </div>
        <h1></h1>
        <img src="../../public/img/main.png" alt="">
    </header>-->
<?php
        require_once("../../webapps/common/header3.php");
    ?>

    <div class="main-container">
        <form>
            <div class="content" id="formBlock">
                <section>
                    <h2>本登録用ログイン</h2>
                    <div class="point_block error" style="display: none;text-align: left">
                        ユーザIDまたはパスワードが正しくありません。<br>
                        ※IDとパスワードについて大文字・小文字も識別します。<br>
                        ※当社で設定できるパスワード桁数は6～20桁です。パスワードの桁数をお確かめ願います。<br>
                    </div>
                    <dl class="user-input">
                        <dt>ID（メールアドレス）</dt>
                        <dd><input name="email" type="text" class="add" maxlength="254"></dd>
                        <dt>パスワード</dt>
                        <dd>
                            <input id="password" name="password" type="password" class="add" maxlength="20" placeholder="半角英数字6～20桁">
                        </dd>
                    </dl>
                    <div class="btn-list-area">
                        <a class="btn-link btn btn-yel" onclick="login()">ログイン</a>
                        <hr>
                        <a class="btn-link btn btn-red" href="../../index.php">トップ画面</a>
                    </div>
                </section>
            </div>
        </form>
    </div>

    <?php
    require_once("../common/footer.php");
    ?>
</div>
</body>
</html>




