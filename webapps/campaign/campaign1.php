<!DOCTYPE html>
<?php
require_once("../../application/common/db/sessioninit.php");

// セッション共通処理
require_once("../../application/common/comUtils/SessionCheck.php");
// DB共通処理をロードする
require_once("../../application/common/db/dbini.php");
// ヘッダーをロードする

if (!session_id()) session_start();
if(isset($_SESSION['isRecruit']) && $_SESSION['isRecruit'] == "point"){
	header("Location: /webapps/user/user.php");
}
?>
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
<script src="./campaign1.js"></script>
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
      <h2>キャンペーン応募</h2>
      <div class="point_block error" id="checkErrortext"></div>
      <div class="point_block applylsit-page">現在のポイント　<span class="point-namber" id="nowPoint">0</span>pt</div>
      <div class="item-select"><input type="hidden" id="update_time" />
        <div class="item-select-item">
          <div class="title">A賞（10名様）</div>
          <div class="image"><img src="../../public/img/pfreebie-a.png" alt="[DOD] キノコテント"></div>
          <div class="name">[DOD] キノコテント</div>
          <div class="point">20ポイント/1口</div>
          <div>数量：<input type="text" id="a_count" data_type="a" value="0" oninput="value=(value.replace(/\D/g,'')==''?'':parseInt(value))" maxlength="10"> 
          <input type="button" name="buttons" id="a_add_button" data_type="a" value="＋"> 
          <input type="button" name="buttons" id="a_cut_button" data_type="a" value="－"></div>
        </div>
        <div class="item-select-item">
          <div class="title">B賞（各色5名様）</div>
          <div class="image"><img src="../../public/img/pfreebie-b.png" alt="[Oregonian Camper] YAVIN 53（ツールボックス）"></div>
          <div class="name">[Oregonian Camper] YAVIN 53（ツールボックス）</div>
          <div class="point">15ポイント/1口</div>
          <div>数量：<input type="text" id="b_count1" data_type="b" color_type="1" value="0" oninput="value=(value.replace(/\D/g,'')==''?'':parseInt(value))" maxlength="10"> 
          <input type="button" name="buttons" id="b_add_button1" data_type="b" color_type="1" value="＋"> 
          <input type="button" name="buttons" id="b_cut_button1" data_type="b" color_type="1" value="－"> ミリタリーグリーン</div>
          <div>数量：<input type="text" id="b_count2" data_type="b" color_type="2" value="0" oninput="value=(value.replace(/\D/g,'')==''?'':parseInt(value))" maxlength="10">
          <input type="button" name="buttons" id="b_add_button2" data_type="b" color_type="2" value="＋"> 
          <input type="button" name="buttons" id="b_cut_button2" data_type="b" color_type="2" value="－"> グレー</div>
        </div>
        <div class="item-select-item">
          <div class="title">C賞（50名様）</div>
          <div class="image"><img src="../../public/img/pfreebie-c.png" alt="[DOD] うさサンドメーカー"></div>
          <div class="name">[DOD] うさサンドメーカー</div>
          <div class="point">10ポイント/1口</div>
          <div>数量：<input type="text" id="c_count" data_type="c" value="0" oninput="value=(value.replace(/\D/g,'')==''?'':parseInt(value))" maxlength="10">
          <input type="button" name="buttons" id="c_add_button" data_type="c" value="＋"> 
          <input type="button" name="buttons" id="c_cut_button" data_type="c" value="－"></div>
        </div>
        <div class="item-select-item">
          <div class="title">D賞（2,000名様）</div>
          <div class="image"><img src="../../public/img/pfreebie-d.png" alt="[キャプテンスタッグ] CAMP HACKロゴ入りオリジナルシェラカップ"></div>
          <div class="name">[キャプテンスタッグ] CAMP HACKロゴ入りオリジナルシェラカップ</div>
          <div class="point">2ポイント/1口</div>
          <div>数量：<input type="text" id="d_count" data_type="d" value="0" oninput="value=(value.replace(/\D/g,'')==''?'':parseInt(value))" maxlength="10">
          <input type="button" name="buttons" id="d_add_button" data_type="d" value="＋"> 
          <input type="button" name="buttons" id="d_cut_button" data_type="d" value="－"></div>
        </div>
        <div class="item-select-item">
          <div class="title">E賞（20,000名様）</div>
          <div class="image"><img src="../../public/img/pfreebie-e.png" alt="ヘパリーゼW粒 引換券"></div>
          <div class="name">ヘパリーゼW粒 引換券</div>
          <div class="point">1ポイント/1口</div>
          <div>数量：<input type="text" id="e_count" data_type="e" value="0" oninput="value=(value.replace(/\D/g,'')==''?'':parseInt(value))" maxlength="10">
          <input type="button" name="buttons" id="e_add_button" data_type="e" value="＋"> 
          <input type="button" name="buttons" id="e_cut_button" data_type="e" value="－">
          </div>
        </div>
      </div>
      <div class="point_block check">応募後の変更はできません、よくご確認の上応募ください。</div>
      <div class="btn-list-area">
        <a class="btn-link btn btn-yel" id="addChange" href="#">応募する</a>
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




