$(function () {
    // #で始まるリンクをクリックしたら実行されます
    $('a[href^="#"]').click(function () {
        // スクロールの速度
        var speed = 400; // ミリ秒で記述
        var href = $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top;
        $('body,html').animate({scrollTop: position}, speed, 'swing');
        return false;
    });
});
//var messageStr = getMessage('Message.C0006');
//var messageStr = getMessage('Message.I0001','仕入日');

/**
 * メッセージを表示する
 * @param strMessageCode メッセージコード
 * @returns
 */
function showMessage(strMessageCode) {
    var strMess = "";
    if (strMessageCode) {
        strMess = MessageDefJsonObject[strMessageCode];
        if (typeof (arguments) == "object") {
            for (i = 1; i < arguments.length; i++) {
                strMess = strMess.replace(MessageReplaceWord, arguments[i]);
            }
        }
    }
    alert(strMess);
}

/**
 * メッセージを取得する
 * @param strMessageCode メッセージコード
 * @returns
 */
function getMessage(strMessageCode) {

    var strMess = "";
    if (strMessageCode) {
        strMess = MessageDefJsonObject[strMessageCode];
        if (typeof (arguments) == "object") {
            for (i = 1; i < arguments.length; i++) {
                strMess = strMess.replace(MessageReplaceWord, arguments[i]);
            }
        }
    }
    return strMess;
}

/**
 * 拠点リンクがクリックされる
 * @param base_cd       拠点コード
 * @param ComChangeBaseUrl　　URL
 * @returns
 */
function baseOnClick(base_cd, ComChangeBaseUrl) {
    var strMess = "";
    if (base_cd) {
        $.ajax({
            type: 'POST',
            url: ComChangeBaseUrl,
            dataType: 'html',
            data: {'base_cd': base_cd},
            error: function () {
                alert('通信エラーです!');
            },
            success: function (data) {
                if (data == 1) {
                    location.reload();
                }
            }
        });
    }
    return strMess;
}

/**
 * セションの拠点コードを画面の値で更新
 * @returns
 */
function updateBaseCd() {
    $.ajax({
        type: 'POST',
        url: '../../../application/common/comUtils/ComChangeBase.php',
        dataType: 'html',
        data: {'base_cd': $("#idcommon_base_cd").html()},
        async: false,
        error: function () {
            alert('通信エラーです!');
        },
        success: function (data) {

        }
    });
    return true;
}

/**
 * 数字（整数、小数の桁数）かどうかをチェックする
 * @param strValue
 * @param intMaxBeforeLength
 * @param intMaxAfterLength
 * @returns
 */
function checkDecimal(strValue, intMaxBeforeLength, intMaxAfterLength) {

//整数の桁数
    strValue = strValue.toString();
    var intBeginLength = strValue.indexOf(".");
    if (intBeginLength <= 0) {
        intBeginLength = strValue.length;
    } else if (intBeginLength > 0 && intBeginLength + 1 == strValue.length) {
        return false;
    }

//小数の桁数(小数点含め)
    var dLenth = strValue.length - intBeginLength;
    if (strValue.indexOf(".") > 0) {
//小数の場合(小数点の桁数をマイナス)
        dLenth = dLenth - 1;
    }

    if (isNaN(strValue)) {
//入力された内容が数字ではない場合
        return false;
    } else if (dLenth > intMaxAfterLength) {
//小数桁数超過の場合
        return false;

    } else if (intBeginLength > intMaxBeforeLength) {
        return false;
    }
    return true;
}


function isMM_ddAndMMDD(strInputDate) {
    if (typeof (strInputDate) == "undefined") {
        strInputDate = "";
    }
    if (typeof (strInputDate) != "string") {
        strInputDate = strInputDate.toString();
    }
    var strDate = strInputDate.trim();
    if (strDate.length < 4) {
        return false;
    } else if (strDate.length == 4) {
//mmdd
        var sysYear = (new Date()).getFullYear();
        strDate = sysYear + '-' + strDate.substring(0, 2) + "-" + strDate.substring(2, 4);
    } else if (strDate.length == 5) {
        var sysYear = (new Date()).getFullYear();
        //mm/dd
        if (strDate.indexOf("/") > 1) {
            strDate = sysYear + '/' + strDate;
        } else {
            return false;
        }
    }

    var arr = new Array();
    if (strDate.indexOf("-") != -1) {
        arr = strDate.toString().split("-");
    } else if (strDate.indexOf("/") != -1) {
        arr = strDate.toString().split("/");
    } else {
        return false;
    }
    if (arr.length != 3) {
        return false;
    }
//yyyy-mm-dd || yyyy/mm/dd
    var date = new Date(strDate);
    if (date.getFullYear() == arr[0] && date.getMonth() == arr[1] - 1 && date.getDate() == arr[2]) {
        return true;
    }
    return false;

}

/**
 * 日付チェック(yyyymmdd || yymmdd || mmdd || yyyy/mm/dd || yyyy-mm-dd || mm-dd || mm/dd)
 * @param strInputDate
 * @returns
 */
function isDate(strInputDate) {
    if (typeof (strInputDate) == "undefined") {
        strInputDate = "";
    }
    if (typeof (strInputDate) != "string") {
        strInputDate = strInputDate.toString();
    }
    var strDate = strInputDate.trim();
    if (strDate.length < 4) {
        return false;
    } else if (strDate.length == 4) {
//mmdd
        var sysYear = (new Date()).getFullYear();
        strDate = sysYear + '-' + strDate.substring(0, 2) + "-" + strDate.substring(2, 4);
    } else if (strDate.length == 5) {
        var sysYear = (new Date()).getFullYear();
//mm-dd || mm/dd
        if (strDate.indexOf("-") > 1) {
            strDate = sysYear + '-' + strDate;
        } else if (strDate.indexOf("/") > 1) {
            strDate = sysYear + '/' + strDate;
        } else {
            return false;
        }
    } else if (strDate.length == 6) {
//yymmdd
        strDate = '20' + strDate;
        strDate = strDate.substring(0, 4) + "-" + strDate.substring(4, 6)
            + "-" + strDate.substring(6, 8);
    } else if (strDate.length == 8) {
//yyyymmdd
        strDate = strDate.substring(0, 4) + "-" + strDate.substring(4, 6)
            + "-" + strDate.substring(6, 8);
    }

    var arr = new Array();
    if (strDate.indexOf("-") != -1) {
        arr = strDate.toString().split("-");
    } else if (strDate.indexOf("/") != -1) {
        arr = strDate.toString().split("/");
    } else {
        return false;
    }
    if (arr.length != 3) {
        return false;
    }
//yyyy-mm-dd || yyyy/mm/dd
    var date = new Date(strDate);
    if (date.getFullYear() == arr[0] && date.getMonth() == arr[1] - 1 && date.getDate() == arr[2]) {
        return true;
    }
    return false;
}

/**
 * 日付より曜日を取得する
 * @param strDate
 * @returns
 */
function getJpWeekText(strDate, regional) {
    var dayNamesShort = "";
    if (regional) {
        dayNamesShort = regional['dayNamesShort'];
    } else {
        dayNamesShort = ['日', '月', '火', '水', '木', '金', '土']
    }
    var date = new Date(strDate);
    var day = date.getDay();
    var dayName = dayNamesShort[day];
    return dayName;
}

/**
 * 二つ日付の大小比較
 * @param date $date1
 * @param date $date2
 * @return int -1:＜    0:＝     1:＞
 */
function compDate(date1, date2) {
    var oDate1 = new Date(date1 + " 00:00:00");
    var oDate2 = new Date(date2 + " 00:00:00");
    var tDate1 = oDate1.getTime();
    var tDate2 = oDate2.getTime();
    if (tDate1 == tDate2) {
        return 0;
    } else if (tDate1 > tDate2) {
        return 1;
    } else {
        return -1;
    }
}

/**
 * 日付をフォーマットする
 * @param strDate
 * @param format
 * @returns
 */
function dateFormat(inputDate, formatStr) {
    if(isNullOrEmpty(formatStr)){
        formatStr = 'yyyy/MM/dd';
    }
    if (typeof (inputDate) == 'string') {
        strDate = inputDate;
        if (strDate.length == 4) {
            //mmdd
            var sysYear = (new Date()).getFullYear();
            strDate = sysYear + '-' + strDate.substring(0, 2) + "-" + strDate.substring(2, 4);
        } else if (strDate.length == 5) {
            //mm-dd || mm/dd
            var sysYear = (new Date()).getFullYear();
            if (strDate.indexOf("-") > 1) {
                strDate = sysYear + '-' + strDate;
            } else if (strDate.indexOf("/") > 1) {
                strDate = sysYear + '/' + strDate;
            } else {
                return "";
            }
        } else if (strDate.length == 6) {
            //yymmdd
            strDate = '20' + strDate;
            strDate = strDate.substring(0, 4) + "-" + strDate.substring(4, 6)
                + "-" + strDate.substring(6, 8);
        } else if (strDate.length == 8) {
            //yyyymmdd
            strDate = strDate.substring(0, 4) + "-" + strDate.substring(4, 6)
                + "-" + strDate.substring(6, 8);
        }
        var date = new Date(strDate);
        return date.format(formatStr);
    } else if (jQuery.type(inputDate) == 'date') {
        return inputDate.format(formatStr);
    } else {
        var date = new Date(inputDate);
        return date.format(formatStr);
    }
}

Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1, //month
        "d+": this.getDate(),    //day
        "h+": this.getHours(),   //hour
        "m+": this.getMinutes(), //minute
        "s+": this.getSeconds(), //second
        "q+": Math.floor((this.getMonth() + 3) / 3),  //quarter
        "S": this.getMilliseconds() //millisecond
    }
    if (/(y+)/.test(format)) format = format.replace(RegExp.$1,
        (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o) if (new RegExp("(" + k + ")").test(format))
        format = format.replace(RegExp.$1,
            RegExp.$1.length == 1 ? o[k] :
                ("00" + o[k]).substr(("" + o[k]).length));
    return format;
}

function isZenKatakana(str){
    str = (str==null)?"":str;
    if(str.match(/^[ァ-ヶー　]+$/)){    //"ー"の後ろの文字は全角スペースです。
        return true;
    }else{
        return false;
    }
}

function notZenKatakana(str){
    str = (str==null)?"":str;
    if(str.match(/[ァ-ヶー　]+$/)){    //"ー"の後ろの文字は全角スペースです。
        return true;
    }else{
        return false;
    }
}


/**
 * 日付が日数をプラス
 * @param date
 * @param days
 * @returns
 */
function addDate(inputDate, days) {
    if (days == undefined || days === '') {
        days = 1;
    }
    return inputDate.setDate(inputDate.getDate() + days);
}

function dateChange(strDate) {
    if (strDate.length == 8) {
        strDate = strDate.substring(0, 4) + "/" + strDate.substring(4, 6) + "/" + strDate.substring(6, 8);
    } else if (strDate.length > 10) {
        strDate = strDate.substring(0, 4) + "/" + strDate.substring(5, 7) + "/" + strDate.substring(8, 10);
    } else if (strDate.length == 6) {
        strDate = "20" + strDate.substring(0, 2) + "/" + strDate.substring(2, 4) + "/" + strDate.substring(4, 6)
    }
    return strDate;
}

function removeDateFormat(strDate) {
    strDate = strDate.substring(0, 4) + strDate.substring(5, 7) + strDate.substring(8, 10);
    return strDate;
}

/**
 * 金额格式化
 * 参数说明：
 * number：要格式化的数字
 * decimals：保留几位小数
 * dec_point：小数点符号
 * thousands_sep：千分位符号
 **/
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.ceil(n * k) / k;
        };

    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    var re = /(-?\d+)(\d{3})/;
    while (re.test(s[0])) {
        s[0] = s[0].replace(re, "$1" + sep + "$2");
    }

    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return "￥" + s.join(dec);
}

function finalNumberFormat(number) {

    if (number === undefined || number == '' || number == null) {
        return '';
    }
    if (typeof number === 'number') {
        number = number.toString();
    }
    if (number.indexOf('.') > 0) {
        var numberArr = number.split('.');
        if (numberArr[1] == 0) {
            return numberArr[0];
        } else {
            return Number(numberArr[0] + "." + numberArr[1].slice(0, 1));
        }
    } else {
        return number;
    }
}

/**
 * 检查下拉列表是否存在relevantCode相应的code
 * @param relevantCode
 * @param dropdownDataList
 * @param listKey
 * @return exist : true, not exist:false
 * **/
function checkDropdownDataListExistRelevantCode(relevantCode, dropdownDataList, listKey) {
    for (let i = 0; i < dropdownDataList.length; i++) {
        if (relevantCode == dropdownDataList[i][listKey]) {
            return true;
        }
    }
    return false;
}

/**
 * mmdd日付チェック(mmdd || mm/dd)
 * @param strInputDate
 * @returns
 */
function isMmddDate(strInputDate) {
    if (typeof (strInputDate) == "undefined") {
        return false;
    }
    if (typeof (strInputDate) != "string") {
        strInputDate = strInputDate.toString();
    }
    var strDate = strInputDate.trim();
    if (strDate.length < 4) {
        return false;
    } else if (strDate.length == 4) {
//mmdd
        var sysYear = (new Date()).getFullYear();
        strDate = sysYear + '/' + strDate.substring(0, 2) + "/" + strDate.substring(2, 4);
    } else if (strDate.length == 5) {
//mm/dd
        if (strDate.indexOf("/") > 1) {
            var sysYear = (new Date()).getFullYear();
            strDate = sysYear + '/' + strDate;
        } else {
            return false;
        }
    } else {
        return false;
    }

    var arr = new Array();
    if (strDate.indexOf("/") != -1) {
        arr = strDate.toString().split("/");
    } else {
        return false;
    }
    if (arr.length != 3) {
        return false;
    }
//yyyy/mm/dd
    var date = new Date(strDate);
    if (date.getFullYear() == arr[0] && date.getMonth() == arr[1] - 1 && date.getDate() == arr[2]) {
        return true;
    }
    return false;
}

/**
 * ｍｍｄｄの日付をyyyymmddの日付にする
 * 年の設定：
 *            本日日付より小さい場合、本年
 *            本日から１ヵ月後の日付より大きい場合、昨年
 *            本日から１ヵ月後の日付は年を跨ぐ場合、来年
 *            本日から１ヵ月後の日付より小さい場合、本年
 *
 * @param inputDate
 * @returns　yyyymmddフォーマットの日付
 */
function mmddDateToyyyymmddDate(inputDate) {
//本日日付より小さい日付または１ヵ月後までは本年（年を跨ぐ場合は来年ね）、１ヶ月より大きい場合は昨年とする。
    var today = new Date();
    var todayYear = today.getFullYear();
    inputDate = dateFormat(inputDate, "MM/dd");
    var myDate = new Date(todayYear + "/" + inputDate);
    var inputYear = 0;
    if (myDate < today) {
//本日日付より小さい日付は本年
        inputYear = todayYear;
    } else {
//本日日付より１ヵ月後の日付
        var todayDate = today.getDate();
        var todayMonth = today.getMonth() + 1;
        var nextOneMonth = todayMonth + 1;
        var nextOneMonthDate = new Date(todayYear, nextOneMonth, todayDate);
        if (myDate > nextOneMonthDate) {
            //１ヶ月より大きい場合は昨年とする
            inputYear = todayYear - 1;
        } else {
            var nextOneMonthYear = nextOneMonthDate.getFullYear();
            if (nextOneMonthYear > todayYear) {
                //年を跨ぐ場合は来年
                inputYear = todayYear + 1;
            } else {
                //１ヵ月後までは本年
                inputYear = todayYear;
            }
        }
    }
    var yyyymmddDate = new Date(inputYear + "/" + inputDate);
    return yyyymmddDate;
}

/**
 * 指定された文字列が null または空の文字列 ("") であるかどうかを示します
 * @param value テストする文字列
 * @returns パラメーターが null または空の文字列 ("") の場合は true。それ以外の場合は false
 */
function isNullOrEmpty(value) {
    if (value == null || value == '') {
        return true;
    } else {
        return false;
    }
}

/**
 * URLにのパラメータを取得する
 * @param paramKey プロパティキー
 * @returns プロパティ値
 */
function getQueryVariable(paramKey) {
//locationオブジェクトのsearch 
    var queryStr = window.location.search;
    if (isNullOrEmpty(queryStr)) {
        return (false);
    }
    query = queryStr.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] == paramKey) {
            return pair[1];
        }
    }
    return (false);
}

/**
 * 全角数字 → 半角数字
 * 使用例　 zenNum2HanNum('１２３') -->   '123'
 * @param str　テストする文字列
 * @returns　半角数字
 */
function zenNum2HanNum(str) {
    str = str + "";
    if (str == "") {
        return str;
    }
    var numStr = str.replace(/[０-９]/g, function (s) {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
    });
    if (!isNaN(numStr)) {
        return Number(numStr);
    } else {
        return numStr;
    }
}


/**
 * 全角かどうかを判断する
 * @param str　テストする文字列
 * @returns　true/false
 */
function isFullAngle(str) {
    if (str != null && str != "") {
        if (str.match(/[^\x00-\xff]/g) == null) {
            return false;
        }
        var str2 = (str.match(/[^\x00-\xff]/g)).join('');
        if (str2 == str ) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * 半角かどうかを判断する
 * @param str　テストする文字列
 * @returns　true/false
 */
function isHalfAngle(str) {
    if (str != null && str != "") {
        if (str.match(/[\x00-\xff]/g) == null) {
            return false;
        }
        var str2 = (str.match(/[\x00-\xff]/g)).join('');
        if (str2 == str) {
            return true;
        } else {
            return false;
        }
    }
}
/**

 * 引数の文字列が全て半角文字であるかチェックする
*/
function containHalf(str) {
    var stringList = str.split('');
    for (var i = 0; i < stringList.length; i++) {
    	var str2 = stringList[i].match(/^[ｱ-ﾝﾞﾟｦｰ]+$/g);
    	if(str2 != null && str2 != ""){
        	return true;
        }
	}
}

/**
 * 判断是否是包含大写字母
 * @param str
 * @returns {boolean}
 */
function isContainsUpperCase(str) {
    rep = /[A-Z]/g;
    if (rep.test(str)) {
        return true;
    } else {
        return false;
    }
}

/**
 *  判断是否包含小写字母
 */
function isContainsLowerCase(str) {
    rep = /[a-z0-9]/g;
    if (rep.test(str)) {
        return true;
    } else {
        return false;
    }
}

/**
 *  メールボックスですか
 */
function isEmail(str) {
    if(str == null || str == ''){
        return false;
    }
    rep = /^([a-z0-9])+([a-z0-9]*[\-\_]*[a-z0-9]*\.*[a-z0-9\-\_\.])*@[a-z0-9\-\_\.]+([\-\_]*[a-z0-9]+)*(\.*[a-z0-9\-\_]*)+/g;
    if (str.match(rep) == null) {
        return false;
    }
    var str2 = str.match(rep).join('');
    if (str2 == str ) {
        return true;
    } else {
        return false;
    }
}

/**
 *  パスワードですか
 */
function isPassword(str) {
    rep = /^[A-Za-z0-9]+$/
    if (rep.test(str)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 半角数字4桁
 */
function isVerCode(str) {
    rep = /^[0-9]+$/;
    if (rep.test(str)) {
        if (str.length != 4) {
            return false;
        }
        return true;
    } else {
        return false;
    }
}


function isNotEnglishAndNumber(str){
    var rep =  /[^\a-\z\A-\Z0-9]/g;
    if(rep.test(str)){
        return true;
    } else {
        return false;
    }
}




/**
 * 数値がイコールかどうか
 *
 * @param num1
 * @param num2
 * @returns　True：＝　False：≠
 */
function isEquelNum(num1, num2) {
    var number1 = 0;
    var number2 = 0;
    if (!isNaN(num1)) {
        number1 = Number(num1);
    } else {
        number1 = num1;
    }
    if (!isNaN(num2)) {
        number2 = Number(num2);
    } else {
        number2 = num2;
    }
    if (number1 == number2)
        return true;
    return false;
}

/**
 * カレンダー日付をMM/ｄｄ（曜日）で設定する
 * @param theDatepicker　カレンダーコントローラー
 * @param currentDate　
 * @returns
 */
function jpMMddDatepickerClose(theDatepicker, currentDate) {
    var date = theDatepicker.val();
    if (isDate(date)) {
        if (date.length == 10) {
            //カレンダーにて日付を選択した場合には、カレンダーの年を採用する
            currentDate = date;
            dayName = getJpWeekText(date, $.datepicker.regional['ja']);
            date = dateFormat(date, "MM/dd");
            theDatepicker.val(date + "(" + dayName + ")");
        } else {
            currentDate = mmddDateToyyyymmddDate(date);
            dayName = getJpWeekText(currentDate, $.datepicker.regional['ja']);
            date = dateFormat(currentDate, "MM/dd");
            theDatepicker.val(date + "(" + dayName + ")");
        }
    } else {
//日付ではなく
        date = dateFormat(currentDate, "MM/dd");
        dayName = getJpWeekText(currentDate, $.datepicker.regional['ja']);
        theDatepicker.val(date + "(" + dayName + ")");
    }
    return currentDate;
}

window.onpageshow = function (event) {
    if (event.persisted) {
        //safariブラウザバック対策
        window.location.reload();
    }
};

function isRealNum(val) {
    // isNaN()函数 把空串 空格 以及NUll 按照0来处理 所以先去除，

    if (val === "" || val == null) {
        return false;
    }
    if (!isNaN(val)) {
        //对于空数组和只有一个数值成员的数组或全是数字组成的字符串，isNaN返回false，例如：'123'、[]、[2]、['123'],isNaN返回false,
        //所以如果不需要val包含这些特殊情况，则这个判断改写为if(!isNaN(val) && typeof val === 'number' )
        return true;
    } else {
        return false;
    }
}

// 表单序列化
$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

function sleep(numberMillis) {
    var now = new Date();
    var exitTime = now.getTime() + numberMillis;
    while (true) {
        now = new Date();
        if (now.getTime() > exitTime)
            return;
    }
}

function checkDateIsValid(datestr) {
    return (new Date(datestr).getDate() == datestr.substring(datestr.length - 2));
}


function handlePasswordInput(id1,id2){

    $('#' + id1).val($('#' + id1).val().replace(/./g,'*'));

    $('#' + id1).keyup(function(e){
        // 获取输入框中的值
        var show_val =  $(this).val();

        show_val = show_val.replace(/[^\x00-\xff]/g,'')

        $(this).val(show_val);
        var index = show_val.lastIndexOf('*');
        //console.log('index:' + index);
        if(index < 0 ){
            index = 0;
        } else {
            index ++;
        }
        var hide_val = $("#" + id2).val();


        var s_length = show_val.length;
        var h_length = hide_val.length;
        if(s_length < h_length){
            var h_index = h_length - ( h_length - s_length);
            hide_val = hide_val.substring(0,h_index);
        }

        hide_val += show_val.substring(index);

        if(e.key == ' '){
            hide_val + ' ';
        }

        $("#" + id2).val(hide_val);
        $(this).val(show_val.replace(/./g,'*'));
        //console.log($('#re-password').val());
    })
}




