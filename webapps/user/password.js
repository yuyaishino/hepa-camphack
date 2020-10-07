var is_click_valid = true;
var changeUserflag = true;
function goGrant(){
	if(is_click_valid){
		is_click_valid = false;
		var form = document.getElementById("form1");
		form.submit();
	}
}
$(function() {


	handlePasswordInput("cpassword-show","cpassword")

	handlePasswordInput("npassword-show","npassword");

	searchData();
	$("#pword_change").click(function(){
		if(changeUserflag){
			changeUserflag = false;
		    var updateData = {};
		    updateData['npassword'] = $('#npassword').val();
		    updateData['cpassword'] = $('#cpassword').val();
		    updateData['update_time'] = $('#update_time').val();
		  
		    if(checkDate(updateData)){
		    	updatePassword(updateData);
		    }
		}
	});
	hidePop();
});
function searchData(){
    $.ajax({
        type: 'POST',
        url: '../../../application/user/password.php',
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
				if (data.status == 403) {
					window.location.replace('../Login.php');
					return;
				}
                showPop(data.message);
                return;
            } else{
            	if(data.responseData != null){
            		$('#email').text(data.responseData.email);
            		$('#update_time').val(data.responseData.update_time);
            	}
            }
        }
    });
}
/**
 * check
 * @param updateData
 * @returns
 */
function checkDate(updateData){
	var flag = true;
	hidePop();
	//
	if(updateData.npassword == null || updateData.npassword == ""){
		checkErrorMessage = getMessage('Message.I0008');
        showPop(checkErrorMessage);
        changeUserflag = true;
		return false;
	}

	if((!isHalfAngle(updateData.npassword)) || (!isPassword(updateData.npassword)) || updateData.npassword.length < 6 || updateData.npassword.length > 20){
		checkErrorMessage = getMessage('Message.I0008');
		showPop(checkErrorMessage);
		changeUserflag = true;
		return false;
	}
	
	if(updateData.npassword != updateData.cpassword){
		checkErrorMessage = getMessage('Message.I0009');
		showPop(checkErrorMessage);
		changeUserflag = true;
		return false;
	}
	
	return flag;
}

/**
 * 変更パスワード
 * @returns
 */
function updatePassword(updateData){
    var postDataStr = JSON.stringify(updateData);
     $.ajax({
         type: 'POST',
         url: '../../../application/user/password.php',
         data: {
             "CSRF_MIDDLE_TOKEN": $("#csrfmiddlewaretoken").val(),
             "operate": "changepw",
             "data": postDataStr
         },
         async: true,
         dataType: "json",
         error: function (jqXHR, textStatus, errorThrown) {
        	 changeUserflag = true;
        	 alert(textStatus);
         },
         success: function (data) {
             console.log(data);
             if(data.status != 1){
				 if (data.status == 403) {
					 window.location.replace('../Login.php');
					 return;
				 }
            	 changeUserflag = true;
                 showPop(data.message);
                 return;
             }  else{
            	 changeUserflag = true;
            	 //showPop(data.message);
            	 window.location.href="./mypage.php"; 
             }
         }
     });
}
function hidePop(){
	$('.error').hide();
	$("#checkErrortext").html("");
}
function showPop(str){
	$('.error').show();
	$("#checkErrortext").html(str);
}