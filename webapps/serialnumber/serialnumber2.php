<?php

require_once("../../application/common/db/sessioninit.php");

// セッション共通処理
require_once("../../application/common/comUtils/SessionCheck.php");
// DB共通処理をロードする
require_once("../../application/common/db/dbini.php");
// ヘッダーをロードする

if (!session_id()) session_start();
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
    <!--[if lt IE 9]>
    <script src="/assets/common/js/html5shiv.min.js"></script><![endif]-->
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->

    <script src="../../public/jquery/jquery.min.js"></script>

    <script src="../../public/js/MessageDef.js"></script>
    <script src="../../public/js/common.js"></script>


    <script type="text/javascript">


        function submit(){

            var code = $('#code').val();
            if(verify(code)){

                $.ajax({
                    url: '../../application/serialnumber/checkVcode.php',
                    type: 'POST',
                    data: {
                        code:code
                    },
                    dataType: 'json',
                    async: false,
                    success: function (result) {
                        if (result.status == 1) {
                            window.location.replace('serialnumber1.php');
                        } else if(result.status == 2){
                            //alert(result.message);
                            showError(result.message);
                            setTimeout(function(){
                                window.location.replace("../../index.php");
                            }, 3000);
                        } else if(result.status == 403){
                            window.location.href="../Login.php";
                        } else if(result.status == 0){
                            showError(result.message)
                        }
                    }
                })

            }
            return false;
        }

        function verify(code){
            // if(!isRealNum(code)){
            //     var errMessageStr = getMessage('Message.I0091');
            //     showError(errMessageStr);
            //     return false;
            // }
            // if(code.length > 4 || code.length < 4){
            //     var errMessageStr = getMessage('Message.I0091');
            //     showError(errMessageStr);
            //     return false;
            // }
            return true;
        }

        function hideError(){
            $('#error').hide();
        }

        function showError(error) {
            $('#error').html(error);
            $('#error').show();
        }



    </script>

</head>

<body id="top">
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
                <div class="point_block applylsit-page">現在のポイント　<span
                            class="point-namber"><?= isset($_SESSION['no_error_point']) ? $_SESSION['no_error_point'] : '0' ?></span>pt
                </div>
                <div class="serial-code">
                    <p>シリアルNo.</p>
                    <div class="serial-code-input">
                        <input type="text" maxlength="4"
                               value="<?= isset($_SESSION['no_error_no1']) ? $_SESSION['no_error_no1'] : '' ?>" disabled>
                        - <input type="text" maxlength="4"
                                 value="<?= isset($_SESSION['no_error_no2']) ? $_SESSION['no_error_no2'] : '' ?>" disabled>
                        - <input type="text" maxlength="4"
                                 value="<?= isset($_SESSION['no_error_no3']) ? $_SESSION['no_error_no3'] : '' ?>" disabled>
                        - <input type="text" maxlength="4"
                                 value="<?= isset($_SESSION['no_error_no4']) ? $_SESSION['no_error_no4'] : '' ?>" disabled></div>
                </div>
                <div class="point_block error" id="error"><?= isset($_SESSION['no_error_message']) ? $_SESSION['no_error_message'] : '' ?></div>
                <div class="select-code">
                    <p class="error">入力エラーが3回連続して発生したため、登録メールアドレスに認証コードを送信しました。<br>認証コードを入力して本人認証することで、シリアルNo.入力が可能となります。
                    </p>
                    <div class="select-code-input">認証コード <input id="code" type="text" maxlength="4"></div>
                </div>
                <div class="btn-list-area">
                    <a class="btn-link btn btn-yel" onclick="submit()">認証</a>
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





