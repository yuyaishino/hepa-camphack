<?php

require_once("../../application/common/db/sessioninit.php");

// セッション共通処理
require_once("../../application/common/comUtils/SessionCheck.php");
// DB共通処理をロードする
require_once("../../application/common/db/dbini.php");
// ヘッダーをロードする

if (!session_id()) session_start();

if(isset($_SESSION['no_error_count'])){
    if($_SESSION['no_error_count'] >= 3){
        header("Location: /webapps/serialnumber/serialnumber2.php");
    }
}

?>

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

    <script src="../../public/js/MessageDef.js"></script>
    <script src="../../public/js/common.js"></script>

    <script type="text/javascript">

        var flag = true;

        $(function () {

            $.ajax({
                url: '../../application/serialnumber/getPoint.php',
                type: 'POST',
                data: {},
                dataType: 'json',
                async: true,
                success: function (result) {
                    if (result.status == 1) {
                        $('.point-namber').html(result.data.point);
                    } else {
                        alert(result.message);
                    }
                }
            })

            $("input[name='no1']").keyup(function () {
                if ($(this).val().length >= 4) {
                    $("input[name='no2']").focus();
                }
            })

            $("input[name='no2']").keyup(function () {
                if ($(this).val().length >= 4) {
                    $("input[name='no3']").focus();
                }
            })

            $("input[name='no3']").keyup(function () {
                if ($(this).val().length >= 4) {
                    $("input[name='no4']").focus();
                }
            })


        })


        function submit() {
            var data = $('form').serializeObject();
            console.log(data);

            if (verify(data) && flag) {
                // 验证通过
                // 提交数据到后台
                flag = false;
                $.ajax({
                    url: '../../application/serialnumber/checkNo.php',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function (result) {
                        console.log(result);
                        if (result.status == 0) {
                            showError(result.message);
                            flag = true;
                        } else if (result.status == 2) {
                            // 请求成功,进行页面跳转
                            // alert('sendEmail to and 2');
                            window.location.replace('serialnumber2.php');
                        } else if(result.status == 403){
                            window.location.replace('../Login.php');
                        } else {
                            flag = true;
                            hideError();
                            $('.no').val('');
                            $('.point-namber').html(result.data.point);
                        }
                    },
                    error: function (a, b, c) {
                        flag = true;
                        console.log(c);
                        console.log(b);
                        console.log(c);
                    }
                })

            }

            return false;
        }

        function verify(data) {

            console.log('verify...');

            if (data.no1.length > 4 || data.no1.length < 4 || !isRealNum(data.no1)) {
                var errMessageStr = getMessage('Message.I0090', '');
                showError(errMessageStr);
                return false;
            }

            if (data.no1.length > 4 || data.no1.length < 4 || !isRealNum(data.no2)) {
                var errMessageStr = getMessage('Message.I0090', '');
                showError(errMessageStr);
                return false;
            }

            if (data.no1.length > 4 || data.no1.length < 4 || !isRealNum(data.no3)) {
                var errMessageStr = getMessage('Message.I0090', '');
                showError(errMessageStr);
                return false;
            }

            if (data.no1.length > 4 || data.no1.length < 4 || !isRealNum(data.no4)) {
                var errMessageStr = getMessage('Message.I0090');
                showError(errMessageStr);
                return false;
            }

            console.log('验证成功');
            return true;
        }


        function hideError() {
            $('.error').hide();
        }

        function showError(error) {
            $('.error').html(error);
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
        <div class="content" id="formBlock">
            <section>
                <h2>シリアルNo.入力</h2>
                <div class="point_block applylsit-page">現在のポイント　<span class="point-namber">0</span>pt</div>
                <form>
                    <div class="serial-code">
                        <p>シリアルNo.</p>
                        <div class="serial-code-input"><input class="no" name="no1" type="tel" maxlength="4"> - <input
                                    class="no" name="no2"
                                    type="tel"
                                    maxlength="4">
                            - <input class="no" name="no3" type="tel" maxlength="4"> - <input class="no" name="no4"
                                                                                               type="tel"
                                                                                               maxlength="4"></div>
                    </div>
                </form>
                <div class="point_block error" style="display: none">エラー表示</div>
                <div class="point_block check" style="margin: 0 auto 0px">半角数字16桁の入力をお願い致します。</div>
                <div class="point_block check">3回連続して入力エラーとなった場合、本人認証が必要となります。</div>
                <div class="btn-list-area">
<!--                    <a class="btn-link btn btn-yel" onclick="submit()">登録</a>-->
                    <hr>
                    <a class="btn-link btn btn-red" href="../user/mypage.php">マイページ</a>
                    <a class="btn-link btn btn-red" href="../../application/Logout.php">ログアウト</a>
                </div>
            </section>
        </div>
    </div>

    <?php
    require_once("../common/footer.php");
    ?>

</div>
</body>
</html>



