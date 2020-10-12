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
    <script>


        function verify() {

            // 一、验证邮箱
            // 1. 是否全部是半角
            // 2. 字母是否都是小写字母
            // 3. 符号仅仅限于  .,-,_
            // 4. 第一个字母是英文
            let email = $("input[name='email']").val();

            // console.log(isFullAngle(email));
            //
            // console.log(isContainsUpperCase(email));
            //
            // console.log(!isContainsLowerCase(email.substring(0,1)));
            //
            // console.log(!isEmail(email));

            // console.log(email.length);
            if (isFullAngle(email) || isContainsUpperCase(email) || !isEmail(email) || !isContainsLowerCase(email.substring(0, 1)) || email.length > 254) {
                showError('ID(メールアドレス）をご確認ください。');
                return false;
            }


            // 二、 验证密码
            let password = $("input[name='password']").val();

            // 1. 是否全部是半角
            // 2.   5以上 、 21 以下
            if (isNotEnglishAndNumber(password) || password.length < 6 || password.length > 20) {
                showError('パスワードは半角英数の6桁～20桁で入力ください。');
                return false;
            }

            let rePassword = $("#re-password").val();
            // 3. 验证确认密码是否一致
            if (password != rePassword) {
                showError('パスワードが一致しません。');
                return false;
            }

            // 三、 氏名 验证
            //  验证是否是全角
            let name_prifix = $("input[name='name_prifix']").val();
            let name_suffix = $("input[name='name_suffix']").val();
            if (!isFullAngle(name_prifix) || !isFullAngle(name_suffix) || containHalf(name_prifix) || containHalf(name_suffix)) {
                showError('全角で入力ください。');
                return false;
            }

            console.log(name_prifix.length);

            if (name_prifix.length > 30 || name_suffix > 30) {
                showError('氏名60桁以内で入力してください。');
                return false;
            }

            // 四、 氏名（フリガナ） 验证
            // 验证是否是全角
            let notation_prifix = $("input[name='notation_prifix']").val();
            let notation_sufix = $("input[name='notation_sufix']").val();

            if (isNullOrEmpty(notation_prifix) || isNullOrEmpty(notation_sufix)) {
                showError('全角カタカナで入力ください。');
                return false;
            }

            if (!isZenKatakana(notation_prifix) || !isZenKatakana(notation_sufix)) {
                showError('全角カタカナで入力ください。');
                return false;
            }

            if (notation_prifix.length > 60 || notation_sufix > 60) {
                showError('カタカナ60桁以内で入力してください。');
                return false;
            }

            // 五、 验证通过
            return true;
        }


        function doSubmit() {
            if (verify()) {
                $.ajax({
                    url: '../../application/sign/SingUp.php',
                    data: $("form").serializeObject(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        console.log(result);
                        if (result.status == 0) { //失败
                            $('.error').show();
                            $('.error').html(result.message);
                        } else {
                            //页面跳转
                            window.location.replace('signupcheck.php');
                        }
                    }, error: function (jq, text, error) {

                    }
                })
            }
            return false;
        }


        function showError(str) {
            $('.error').show();
            $('.error').html(str);
        }


    </script>
    <script src="../../public/js/common.js"></script>
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
        <form action="../../application/sign/SingUp.php" method="post">
            <div class="content" id="formBlock">
                <section>
                    <h2>新規登録</h2>
                    <div class="point_block error" style="display: none">エラー表示</div>
                    <dl class="user-input">
                        <dt>ID（メールアドレス）</dt>
                        <dd><input name="email" type="text" class="add"
                                   value="<?= array_key_exists('temp_email', $_SESSION) ? $_SESSION['temp_email'] : '' ?>" maxlength="254">
                        </dd>
                        <dt>パスワード</dt>
                        <dd><input id="password" name="password" type="password" class="add"
                                   value="<?= array_key_exists('temp_password', $_SESSION) ? $_SESSION['temp_password'] : '' ?>">
                        </dd>
                        <dt>パスワード（確認用）</dt>
                        <dd><input id="re-password" type="password" class="add"
                                   value="<?= array_key_exists('temp_password', $_SESSION) ? $_SESSION['temp_password'] : '' ?>">
                        </dd>
                        <dt>氏名</dt>
                        <dd><input name="name_prifix" type="text" class="name"
                                   value="<?= array_key_exists('temp_name_prifix', $_SESSION) ? $_SESSION['temp_name_prifix'] : '' ?>"
                                   maxlength="30">　<input name="name_suffix" type="text"
                                                          class="name"
                                                          value="<?= array_key_exists('temp_name_suffix', $_SESSION) ? $_SESSION['temp_name_suffix'] : '' ?>"
                                                          maxlength="30"></dd>
                        <dt>氏名（フリガナ）</dt>
                        <dd><input type="text" name="notation_prifix" class="name"
                                   value="<?= array_key_exists('temp_notation_prifix', $_SESSION) ? $_SESSION['temp_notation_prifix'] : '' ?>"
                                   maxlength="30">　<input type="text"
                                                          name="notation_sufix"
                                                          class="name"

                                                          value="<?= array_key_exists('temp_notation_sufix', $_SESSION) ? $_SESSION['temp_notation_sufix'] : '' ?>"
                                                          maxlength="30"></dd>
                    </dl>
                    <div class="btn-list-area">
                        <a class="btn-link btn btn-yel" onclick="doSubmit()">登録</a>
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