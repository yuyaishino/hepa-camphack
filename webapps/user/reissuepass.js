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
	$("#pword_change").click(function(){
		if(changeUserflag){
			changeUserflag = false;
		    var updateData = {};
		    updateData['email'] = $('#email').val();
		  
		    if(checkDate(updateData)){
		    	updatePassword(updateData);
		    }
		}
	});
	hidePop();
});

/**
 * check
 * @param updateData
 * @returns
 */
function checkDate(updateData){
	var flag = true;
	hidePop();
	//
	if(updateData.email == null || updateData.email == ""){
		checkErrorMessage = getMessage('Message.I0010');
        showPop(checkErrorMessage);
        changeUserflag = true;
		return false;
	}

/*	if(!isEmail(updateData.email)){
		checkErrorMessage = getMessage('Message.I0008');
		showPop(checkErrorMessage);
		changeUserflag = true;
		return false;
	}*/
	
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
         url: '../../../application/user/reissuepass.php',
         data: {
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
            	 changeUserflag = true;
                 showPop(data.message);
                 return;
             } else{
            	 changeUserflag = true;
            	 //showPop(data.message);
            	 window.location.href="../Login.php"; 
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