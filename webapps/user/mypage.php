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
    <!--[if lt IE 9]>
    <script src="/assets/common/js/html5shiv.min.js"></script><![endif]-->
    <!--[if lt IE 9]>
    <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->

    <script src="../../public/jquery/jquery.min.js"></script>
    <script src="../../public/js/common.js"></script>
</head>
<script type="text/javascript">
    var is_click_valid = true;

    $(function(){

        $.ajax({
            url:'../../application/user/MyPage.php',
            type:'POST',
            data:{},
            dataType:'json',
            async:false,
            success:function(result) {
                console.log(result);
                if(result.status == 0){

                } else {
                    let data = result.data;

                    $('.mypage-name').html(data.name_prifix + data.name_suffix + '様');
                    $('.point-namber').html(data.point == null ? 0 : data.point);

                    $('.mypage-select').html('');


                    var dataArray = new Array();
                    dataArray[0] = new Array();
                    dataArray[1] = new Array();
                    dataArray[2] = new Array();
                    dataArray[3] = new Array();
                    dataArray[4] = new Array();

                    for(var i = 0 ; i < data.prizeList.length ; i ++){

                        switch (data.prizeList[i].type) {
                            case '0':
                                var typeData = {
                                    typeStr:'A賞',
                                    time:data.prizeList[i].time,
                                    apply_count: data.prizeList[i].apply_count
                                };
                                dataArray[0].push(typeData);
                                break;
                            case '1':
                                var typeData = {
                                    typeStr:'B賞',
                                    time:data.prizeList[i].time,
                                    color:data.prizeList[i].color,
                                    apply_count: data.prizeList[i].apply_count
                                }
                                dataArray[1].push(typeData);
                                break;
                            case '2':
                                var typeData = {
                                    typeStr:'C賞',
                                    time:data.prizeList[i].time,
                                    apply_count: data.prizeList[i].apply_count
                                };
                                dataArray[2].push(typeData);
                                break;
                            case '3':
                                var typeData = {
                                    typeStr:'D賞',
                                    time:data.prizeList[i].time,
                                    apply_count: data.prizeList[i].apply_count
                                };
                                dataArray[3].push(typeData);
                                break;
                            case '4':
                                var typeData = {
                                    typeStr:'E賞',
                                    time:data.prizeList[i].time,
                                    apply_count: data.prizeList[i].apply_count
                                };
                                dataArray[4].push(typeData);
                                break;
                        }
                    }

                    console.log(dataArray);
                    var html = '';
                    html ="<dt>賞品</dt><dd><span class='date'>日付</span><span>口数</span></dd>";
                    $('.mypage-select').append(html);
                    // A 赏
                    html = '';
                    if(dataArray[0].length > 0){
                        
//                        html += '<dt>' + dataArray[0][0].typeStr + '</dt>' +
//                                    '<dd><span class="date">' + dataArray[0][0].time + '</span>' + 
//                                    '<span>' + dataArray[0][0].apply_count + '口</span></dd>'; 
                        for(var i = 0 ; i < dataArray[0].length; i++){
                            html += '<dt>' + dataArray[0][i].typeStr + '</dt>' +
                                '<dd><span class="date">' + dataArray[0][i].time + '</span>' + 
                                    '<span>' + dataArray[0][i].apply_count + '口</span></dd>'; 
                        }
                    } else {
                        html = '<dt>A賞</dt>' +
                            '<dd>ございません。</dd>';
                    }
                    $('.mypage-select').append(html);

                    // B 赏
                    html ='';
                    if(dataArray[1].length > 0){
//                        for(var i = 0 ; i < dataArray[1].length; i++){
//                            if(dataArray[1][i].color == 1){
//                                html += ' <dt>B賞</dt>\n' +
//                                    '<dd>' +
//                                    '   <p><span class="color">ミリタリーグリーン&nbsp;&nbsp;</span><span class="colordate">' + dataArray[1][0].time + '</span><span class="num">' + dataArray[1][0].apply_count + '口</span></p>\n' +
//                                    '</dd>';
//                            } else if (dataArray[1][i].color == 2){
//                                html += ' <dt>B賞</dt>\n' +
//                                    '<dd>\n' +
//                                    '   <p><span class="date">グレー&nbsp;&nbsp;' + dataArray[1][0].time + '</span><span>' + dataArray[1][0].apply_count + '口</span></p>\n' +
//                                    '</dd>';
//                            }
//                            
//                        }
//                        for(var i = 0 ; i < dataArray[1].length; i++){
//                            for(var j = 0 ;  j < dataArray[1][i].apply_count; j++){
//                                if(dataArray[1][i].color == 1){
//                                    html += ' <dt>B賞</dt>\n' +
//                                        '                            <dd>' +
//                                        '                            <p>ミリタリーグリーン&nbsp;&nbsp;' + dataArray[1][i].time + '</p>\n' +
//                                        '                            </dd>';
//                                } else if (dataArray[1][i].color == 2){
//                                    html += ' <dt>B賞</dt>\n' +
//                                        '                            <dd>\n' +
//                                        '                            <p>グレー&nbsp;&nbsp;' + dataArray[1][i].time + '</p>\n' +
//                                        '                            </dd>';
//                                }
//                            }
//                        }
                        for(var i = 0 ; i < dataArray[1].length; i++){
                            if(dataArray[1][i].color == 1){
                                html += ' <dt>B賞</dt>\n' +
                                    '<dd>' +
                                    '   <p><span class="colordate">' + dataArray[1][i].time + '</span><span class="color">ミリタリーグリーン&nbsp;&nbsp;</span><span class="num">' + dataArray[1][i].apply_count + '口</span></p>\n' +
                                    '</dd>';
                            } else if (dataArray[1][i].color == 2){
                                html += ' <dt>B賞</dt>\n' +
                                    '<dd>\n' +
                                    '   <p><span class="colordate">' + dataArray[1][i].time + '</span><span class="color">グレー&nbsp;&nbsp;</span><span class="num">' + dataArray[1][i].apply_count + '口</span></p>\n' +
                                    '</dd>';
                            }
                        }
                    } else {
                        html = '<dt>B賞</dt>' +
                            '<dd>ございません。</dd>';
                    }
                    $('.mypage-select').append(html);


                    // C 赏
                    html ='';
                    if(dataArray[2].length > 0){
//                     html += '<dt>' + dataArray[2][0].typeStr + '</dt>' +
//                                    '<dd><span class="date">' + dataArray[2][0].time + '</span>' + 
//                                    '<span>' + dataArray[2][0].apply_count + '口</span></dd>'; 
//                        for(var i = 0 ; i < dataArray[2].length; i++){
//                            for(var j = 0 ; j < dataArray[2][i].apply_count; j++){
//                                html += '<dt>' + dataArray[2][i].typeStr + '</dt>' +
//                                    '<dd>' + dataArray[2][i].time + '</dd>';
//                            }
//                        }
                        for(var i = 0 ; i < dataArray[2].length; i++){
                            html += '<dt>' + dataArray[2][i].typeStr + '</dt>' +
                                '<dd><span class="date">' + dataArray[2][i].time + '</span>' + 
                                    '<span>' + dataArray[2][i].apply_count + '口</span></dd>'; 
                        }
                    } else {
                        html = '<dt>C賞</dt>' +
                            '<dd>ございません。</dd>';
                    }
                    $('.mypage-select').append(html);


                    // D 赏
                    html ='';
                    if(dataArray[3].length > 0){
//                         html += '<dt>' + dataArray[3][0].typeStr + '</dt>' +
//                                    '<dd><span class="date">' + dataArray[3][0].time + '</span>' + 
//                                    '<span>' + dataArray[3][0].apply_count + '口</span></dd>'; 
//                        for(var i = 0 ; i < dataArray[3].length; i++){
//                            for(var j = 0 ; j < dataArray[3][i].apply_count; j++){
//                                html += '<dt>' + dataArray[3][i].typeStr + '</dt>' +
//                                    '<dd>' + dataArray[3][i].time + '</dd>';
//                            }
//                        }
                        for(var i = 0 ; i < dataArray[3].length; i++){
                            html += '<dt>' + dataArray[3][i].typeStr + '</dt>' +
                                '<dd><span class="date">' + dataArray[3][i].time + '</span>' + 
                                    '<span>' + dataArray[3][i].apply_count + '口</span></dd>'; 
                        }
                    } else {
                        html = '<dt>D賞</dt>' +
                            '<dd>ございません。</dd>';
                    }
                    $('.mypage-select').append(html);

                    // E 赏
                    html ='';
                    if(dataArray[4].length > 0){
//                         html += '<dt>' + dataArray[4][0].typeStr + '</dt>' +
//                                    '<dd><span class="date">' + dataArray[4][0].time + '</span>' + 
//                                    '<span>' + dataArray[4][0].apply_count + '口</span></dd>'; 
//                        for(var i = 0 ; i < dataArray[4].length; i++){
//                            for(var j = 0 ; j < dataArray[4][i].apply_count; j++){
//                                html += '<dt>' + dataArray[4][i].typeStr + '</dt>' +
//                                    '<dd>' + dataArray[4][i].time + '</dd>';
//                            }
//                        }
                        for(var i = 0 ; i < dataArray[4].length; i++){
                            html += '<dt>' + dataArray[4][i].typeStr + '</dt>' +
                                '<dd><span class="date">' + dataArray[4][i].time + '</span>' + 
                                    '<span>' + dataArray[4][i].apply_count + '口</span></dd>'; 
                        }
                    } else {
                        html = '<dt>E賞</dt>' +
                            '<dd>ございません。</dd>';
                    }
                    $('.mypage-select').append(html);

                }
            },error:function(j,t,e){
                console.log(e);
            }
        })

    })

    function goGrant() {
        if (is_click_valid) {
            is_click_valid = false;
            var form = document.getElementById("form1");
            form.submit();
        }
    }
</script>
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
                <h2>マイページ</h2>
                <div class="mypage-name"></div>
                <div class="point_block applylsit-page">現在のポイント　<span class="point-namber">0</span>pt</div>
                <p class="mypage-name-p">応募状況</p>
                <dl class="mypage-select">
<!--                    <dt>賞品</dt>
                    <dd><span class="date">日付</span><span>口数</span></dd>
                    <dt>A賞</dt>
                    <dd><span class="date">2020/10/30</span><span>99999口</span></dd>
                    <dt>B賞</dt>
                    <dd>
                        <p>ミリタリーグリーン：2020/10/30</p><span>99999口</span>
                        <p>シルバー：ございません。</p><span>99999口</span>
                        <p><span class="date">ミリタリーグリーン：2020/10/30</span><span>99999口</span></p>
                        <p><span class="date">シルバー：ございません。</span><span>99999口</span></p>
                    </dd>
                    <dt>C賞</dt>
                    <dd>ございません。</dd>
                    <dt>D賞</dt>
                    <dd>ございません。</dd>
                    <dt>E賞</dt>
                    <dd>2020/10/30</dd>-->
                </dl>
                <div class="btn-list-area">
                    <a class="btn-link btn btn-yel" href="../serialnumber/serialnumber1.php">シリアルNo.入力</a>
                    <a class="btn-link btn btn-yel" href="../campaign/campaign1.php">応募する</a>
                    <a class="btn-link btn btn-yel" href="userinfo.php">会員情報・変更</a>
                    <a class="btn-link btn btn-yel" href="password.php">パスワード変更</a>
                    <hr>
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




