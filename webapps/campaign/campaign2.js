var is_click_valid = true;
var changeUserflag = true;
var countDiff = 0;
function goGrant(){
	if(is_click_valid){
		is_click_valid = false;
		var form = document.getElementById("form1");
		form.submit();
	}
}

$(function() {
	searchData();
	$("input[name='buttons']").click(function(){
		buttonChange(this);
	});
	$("#addChange").click(function(){
		if(changeUserflag){
			changeUserflag = false;
		    var updateList = [];
		    var verificationCode = $("#verificationCode").val();
	   
		    if(checkDate(verificationCode)){
		    	addAndUpdate(verificationCode);
		    }
		}
	});
	hidePop();
})
/**
 * check
 * @param updateData
 * @returns
 */
function checkDate(newCode){
	var flag = true;
	hidePop();
/*	if(newCode == null || newCode == ""){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0013', "");
        showPop(checkErrorMessage);
        return false;
	}
	if(!isVerCode(newCode)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0095', "");
        showPop(checkErrorMessage);
        return false;
	}*/
	return flag;
}

/**
 * 変更する
 * @returns
 */
function addAndUpdate(verificationCode){
    console.log("---------addAndUpdate---------",verificationCode)
    var updateData = {};
    updateData['verificationCode'] = verificationCode;
    updateData['countDiff'] = countDiff;
    var postDataStr = JSON.stringify(updateData);
     $.ajax({
         type: 'POST',
         url: '../../../application/campaign/campaign2.php',
         data: {
             "CSRF_MIDDLE_TOKEN": $("#csrfmiddlewaretoken").val(),
             "operate": "add",
             "data": postDataStr
         },
         async: true,
         dataType: "json",
         error: function (jqXHR, textStatus, errorThrown) {
        	 changeUserflag = true;
        	 alert(textStatus);
         },
         success: function (data) {
        	 /*
             console.log(data);*/
             if(data.status == 1){
            	 window.location.href="../user/mypage.php"; 
             }else if(data.status == 2){
            	 window.location.href="../user/user.php"; 
             } else if(data.status == 403){
				 window.location.href="../Login.php";
			 } else {
              	if(data.message == "一定時間が経過しましたので、ログアウトしました。"){
                    showPop(data.message);
          			setTimeout(function(){
          				window.location.href="../Login.php";
          			},3000);
              	}else if(data.message == "認証コードが一致していません。"){
                  	countDiff++;
                  	changeUserflag = true;
                  	showPop(data.message);
              	}else if(data.message == "認証コードの不一致が3回となったためログアウトします。"){
          			showPop(data.message);
          			setTimeout(function(){
          				window.location.href="../Login.php";
          			},3000);
              	}
                return;
             }
         }
     });
}
function searchData(){
    $.ajax({
        type: 'POST',
        url: '../../../application/campaign/campaign2.php',
        data: {
            "CSRF_MIDDLE_TOKEN": $("#csrfmiddlewaretoken").val(),
            "operate": "search",
            "data": ""
        },
        async: true,
        dataType: "json",
        error: function (jqXHR, textStatus, errorThrown) {
       	 alert(textStatus);
        },
        success: function (data) {
            console.log(data);
            if(data.status != 1){
                showPop(data.message);
                return;
            } else if(data.status == 403){
				window.location.href="../Login.php";
			} else{
            	if(data.responseData != null){
            		showData(data.responseData.campaign);
            	}
            }
        }
    });
}
function showData(data){
	var html = "";
	for (var i = 0; i < data.length; i++) {
		switch (data[i].type) {
		case "A":
			html = html + "<P>A賞　"+data[i].apply_count+"口</p>";
			break;
		case "B":
			html = html + "<P>B賞　"+data[i].apply_count+"口　";
			if(data[i].color == '1'){
				html = html + "ミリタリーグリーン</p>";
			}else if(data[i].color == '2'){
				html = html + "グレー</p>";
			}
			break;
		case "C":
			html = html + "<P>C賞　"+data[i].apply_count+"口</p>";
			break;
		case "D":
			html = html + "<P>D賞　"+data[i].apply_count+"口</p>";
			break;
		case "E":
			html = html + "<P>E賞　"+data[i].apply_count+"口</p>";
			break;
		default:
			break;
		}
		
	}
	html = html+"<p>が選ばれています。</p>";
	$("#select_check").html(html);
}
function hidePop(){
	$('.error').hide();
	$("#checkErrortext").html("");
}
function showPop(str){
	$('.error').show();
	$("#checkErrortext").html(str);
}