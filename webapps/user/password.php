<?php
// セッション共通処理
require_once("../../application/common/db/sessioninit.php");

require_once("../../application/common/comUtils/SessionCheck.php");
// DB共通処理をロードする
require_once("../../application/common/db/dbini.php");
// ヘッダーをロードする

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
<script src="../../public/jquery/jquery.min.js"></script>
<script src="../../public/js/common.js"></script>
<script src="../../public/js/MessageDef.js"></script>
<script src="./password.js"></script>
</head>
<body id="top">
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQZV8LK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="wrap">

<!--<header>
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
      <h2>パスワード変更</h2>
      <div class="point_block error" id="checkErrortext"></div>
      <dl class="password-input">
        <dt>ID（メールアドレス）</dt>
        <dd id="email" maxlength="254"></dd>
        <dt>パスワード</dt>
        <dd>
        <input type="password" id="npassword" maxlength="20" placeholder="半角英数字6～20桁">
        <input type="hidden" id="update_time" />
        </dd>
        <dt>パスワード（確認用）</dt>
        <dd>
            <input type="password" id="cpassword" maxlength="20" placeholder="半角英数字6～20桁" >
        </dd>
      </dl>
      <div class="btn-list-area">
        <a class="btn-link btn btn-yel" id="pword_change" href="#">パスワード変更</a>
        <hr>
        <a class="btn-link btn btn-red" href="./mypage.php">マイページ</a>
      </div>
    </section>
  </div>
</div>

    <?php
    require_once("../common/footer.php");
    ?>

</div>
</body>



