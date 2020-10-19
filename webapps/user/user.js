var changeUserflag = true;
var is_click_valid = true;
function goGrant(){
	if(is_click_valid){
		is_click_valid = false;
		var form = document.getElementById("form1");
		form.submit();
	}
}

$(function() {

    $("#year").click(function(){
        //alert(1);
        $("#aa").select(); // フォーカスを当てる
    });
	searchData();
	$("#changeUser").click(function(){
		if(changeUserflag){
			changeUserflag = false;
		    var updateData = {};
		    updateData['old_id'] = $('input[name="old_id"]').val();
		    updateData['update_time'] = $('input[name="update_time"]').val();
		    updateData['name_prifix'] = $('input[name="name_prifix"]').val();
		    updateData['name_suffix'] = $('input[name="name_suffix"]').val();
		    updateData['notation_prifix'] = $('input[name="notation_prifix"]').val();
		    updateData['notation_sufix'] = $('input[name="notation_sufix"]').val();
		    updateData['year'] = $("#year").val();
		    updateData['month'] = $("#month").val();
		    updateData['day'] = $("#day").val();
		    updateData['postcode'] = $('input[name="postcode"]').val();
		    updateData['address1'] = $('input[name="address1"]').val();
		    updateData['address2'] = $('input[name="address2"]').val();
		    updateData['address3'] = $('input[name="address3"]').val();
		    updateData['tel'] = $('input[name="tel"]').val();
		    updateData['sex'] = $('input[name="sec"]:checked').val();
		    if(checkDate(updateData)){
		    	addAndUpdate(updateData);
		    }
		}
	});
	$("#postcode_button").click(function(){
		var postcode = $('input[name="postcode"]').val();
		if(postcode == null || postcode == ""){
			$('input[name="address1"]').val("");
		}else{
	     $.ajax({
	         type: 'POST',
	         url: '../../../application/user/Postcode.php',
	         data: {
/*	             "CSRF_MIDDLE_TOKEN": $("#csrfmiddlewaretoken").val(),
*/	             "data": postcode
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
	                 showPop(data.message);
	                 return;
	             }  else{
	            	 if(data.responseData != null){
		            	 if(data.responseData.thirteen == 1){
		            		 $('input[name="address1"]').val(data.responseData.province+""+data.responseData.city);
		            	 }else{

		            		 if(data.responseData.village == '以下に掲載がない場合'){
		            			 $('input[name="address1"]').val(data.responseData.province+""+data.responseData.city);
		            		 }else{
		            			 var village = data.responseData.village;
		            			 var rt = village.split("（");
		            			 
		            		     $('input[name="address1"]').val(data.responseData.province+""+data.responseData.city+rt[0]);
		            		 }
		                 }
	            	 }else{
	            		 $('input[name="address1"]').val("");
	            	 }
	             }
	         }
	     });
		}
	});
	hidePop();
})
/**
 * check
 * @param updateData
 * @returns
 */
function checkDate(updateData){
	var flag = true;
	hidePop();
	//
	if(updateData.name_prifix == null || updateData.name_prifix == ""){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0020');
        showPop(checkErrorMessage);
		return false;
	}
	if(updateData.name_suffix == null || updateData.name_suffix == ""){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0020');
		showPop(checkErrorMessage);
		return false;
	}
	if(updateData.notation_prifix == null || updateData.notation_prifix == ""){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0021');
		showPop(checkErrorMessage);
		return false;
	}
	if(updateData.notation_sufix == null || updateData.notation_sufix == ""){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0021');
		showPop(checkErrorMessage);
		return false;
	}
	
	if(!isFullAngle(updateData.name_prifix) || containHalf(updateData.name_prifix)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0020');
		showPop(checkErrorMessage);
		return false;
	}
	if(!isFullAngle(updateData.name_suffix) || containHalf(updateData.name_suffix)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0020');
		showPop(checkErrorMessage);
		return false;
	}
	if(!isFullAngle(updateData.notation_prifix)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0021');
		showPop(checkErrorMessage);
		return false;
	}
	if(!isFullAngle(updateData.notation_sufix)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0021');
		showPop(checkErrorMessage);
		return false;
	}
	if(!isZenKatakana(updateData.notation_prifix)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0021');
		showPop(checkErrorMessage);
		return false;
	}
	if(!isZenKatakana(updateData.notation_sufix)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0021');
		showPop(checkErrorMessage);
		return false;
	}
	if(isNullOrEmpty(updateData.year) || isNullOrEmpty(updateData.month) || isNullOrEmpty(updateData.day)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0005');
		showPop(checkErrorMessage);
		return false;
	}
	if(updateData.year <= 1930 || updateData.year >= 2021){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0005');
		showPop(checkErrorMessage);
		return false;
	}
	var datestr = updateData.year;
	if(updateData.month < 10){
		datestr = datestr + "-0" + updateData.month;
	}else{
		datestr = datestr + "-" + updateData.month;
	}
	if(updateData.day < 10){
		datestr = datestr + "-0" + updateData.day;
	}else{
		datestr = datestr + "-" + updateData.day;
	}
	if (!checkDateIsValid(datestr)){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0005');
		showPop(checkErrorMessage);
		return false;
	}
//	if(isNullOrEmpty(updateData.postcode)){
//		changeUserflag = true;
//		checkErrorMessage = getMessage('Message.I0006');
//		showPop(checkErrorMessage);
//		return false;
//	}
        if(!isNullOrEmpty(updateData.postcode)){
            if(!isNumber(updateData.postcode) || updateData.postcode.length != 7){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0006');
		showPop(checkErrorMessage);
		return false;
            }
        }
	
//	if(isNullOrEmpty(updateData.address1)){
//		changeUserflag = true;
//		checkErrorMessage = getMessage('Message.I0022');
//		showPop(checkErrorMessage);
//		return false;
//	}
//	if(isNullOrEmpty(updateData.tel)){
//		changeUserflag = true;
//		checkErrorMessage = getMessage('Message.I0007');
//		showPop(checkErrorMessage);
//		return false;
//	}
        if(!isNullOrEmpty(updateData.tel)){
            if(!isNumber(updateData.tel) || (updateData.tel.length != 11 && updateData.tel.length != 10)){
                    changeUserflag = true;
                    checkErrorMessage = getMessage('Message.I0007');
                    showPop(checkErrorMessage);
                    return false;
            }
        }
	return flag;
}

/**
 * 変更する
 * @returns
 */
function addAndUpdate(updateData){
    console.log("---------addAndUpdate---------",updateData)
    var postDataStr = JSON.stringify(updateData);
     $.ajax({
         type: 'POST',
         url: '../../../application/user/user.php',
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
        	 changeUserflag = true;
             console.log(data);
             if(data.status != 1){
				 if (data.status == 403) {
					 window.location.replace('../Login.php');
					 return;
				 }
				 showPop(data.message);
                 return;
             } else{
            	 window.location.href="./userinfo.php"; 
             }
         }
     });
}
function searchData(){
    $.ajax({
        type: 'POST',
        url: '../../../application/user/user.php',
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
            }  else{
            	if(data.responseData != null){
            		showData(data.responseData);
            	}
            }
        }
    });
}
function showData(data){
  	 $("#old_id").val(data.id);
   	 $("#update_time").val(data.update_time);
	 $('input[name="name_prifix"]').val(data.name_prifix);
	 $('input[name="name_suffix"]').val(data.name_suffix);
	 $('input[name="notation_prifix"]').val(data.notation_prifix);
	 $('input[name="notation_sufix"]').val(data.notation_sufix);
         if(data.year === undefined){
             $("#year").val("1980");
         }else{
             $("#year").val(data.year);
         }
         if(data.year === undefined){
             $("#month").val("1");
         }else{
             $("#month").val(data.month);
         }
         if(data.year === undefined){
             $("#day").val("1");
         }else{
             $("#day").val(data.day);
         }
	 $('input[name="postcode"]').val(data.postcode);
	 $('input[name="address1"]').val(data.address1);
	 $('input[name="address2"]').val(data.address2);
	 $('input[name="address3"]').val(data.address3);
	 $('input[name="tel"]').val(data.tel);
	 $('input[name="sec"][value='+data.sex+']').attr("checked",true);
}
function hidePop(){
	$('.error').hide();
	$("#checkErrortext").html("");
}
function showPop(str){
	$('.error').show();
	$("#checkErrortext").html(str);
}