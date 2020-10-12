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
<script src="../../public/jquery/jquery.min.js"></script>
<script src="../../public/js/common.js"></script>
<script src="../../public/js/MessageDef.js"></script>
<script src="./userinfo.js"></script>
</head>

<body id="top">
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
      <h2>会員情報</h2>
      <div class="point_block error" id="checkErrortext">エラー表示</div>
      <dl class="user-input">
        <dt>氏名</dt>
        <dd id="name_prifix" ></dd>
        <dt>氏名（フリガナ）</dt>
        <dd id="notation_prifix"></dd>
        <dt>生年月日</dt>
        <dd id="birthdate" class="gu-dd"></dd>
        <dt>郵便番号</dt>
        <dd id="postcode" class="gu-dd"></dd>
        <dt>住所1</dt>
        <dd id="address1" class="gu-dd"></dd>
        <dt>住所2</dt>
        <dd id="address2" class="gu-dd"></dd>
        <dt>住所3</dt>
        <dd id="address3" class="gu-dd"></dd>
        <dt>電話番号</dt>
        <dd id="tel" class="gu-dd"></dd>
        <dt>性別</dt>
        <dd id="sex" class="gu-dd"></dd>
      </dl>
      <div class="btn-list-area">
        <a class="btn-link btn btn-yel" href="./user.php">変更する</a>
        <hr>
        <a class="btn-link btn btn-red" href="./mypage.php">マイページ</a>
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



