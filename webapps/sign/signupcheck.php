<?php
// セッション共通処理
require_once("../../application/common/db/sessioninit.php");
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
    <script src="../../public/js/common.js"></script>
    <script type="text/javascript">

        var flag = true;

        function send() {

            if (flag) {
                flag = false;
                $.ajax({
                    url: '../../application/sign/SingupCheck.php',
                    //data:{},
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        console.log(result);
                        if (result.status == 0) { //失败
                            flag = true;
                            $('.error').show();
                            $('.error').html(result.message);
                        }  else {
                            //页面跳转
                            window.location.replace("registcomp.php");
                        }
                    }, error: function (jq, text, error) {

                    }
                })
            }
            return false;
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
                <h2>新規登録（登録内容確認）</h2>
                <div class="point_block error" style="display: none">エラー表示</div>
                <dl class="user-input">
                    <dt>ID</dt>
                    <dd><?=isset($_SESSION['temp_email']) ? $_SESSION['temp_email'] : '' ?> </dd>
                    <dt>パスワード</dt>
                    <dd><?php
                        if(isset($_SESSION['temp_password'])){
                            for($i = 0 ; $i <strlen($_SESSION['temp_password']) ; $i++){
                                echo '*';
                            }
                        }
                        ?>

                    </dd>
                    <dt>氏名</dt>
                    <dd><?= isset($_SESSION['temp_name_prifix']) ? $_SESSION['temp_name_prifix'] : '' ?>　<?=isset($_SESSION['temp_name_suffix']) ? $_SESSION['temp_name_suffix'] : '' ?></dd>
                    <dt>氏名（フリガナ）</dt>
                    <dd><?= isset($_SESSION['temp_notation_prifix']) ? $_SESSION['temp_notation_prifix'] : '' ?>　<?= isset($_SESSION['temp_notation_sufix']) ? $_SESSION['temp_notation_sufix'] : '' ?></dd>
                </dl>
                <div class="btn-list-area">
                    <a class="btn-link btn btn-yel" onclick="send()">仮登録メール送信</a>
                    <hr>
                    <a class="btn-link btn btn-red" href="signup.php">戻る</a>
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





