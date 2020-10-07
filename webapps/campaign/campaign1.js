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
	searchData();
	$("input[name='buttons']").click(function(){
		buttonChange(this);
	});
	$("#addChange").click(function(){
		if(changeUserflag){
			changeUserflag = false;
		    var updateList = [];
		    var nowPoint = Number($("#nowPoint").text());
		    //0:A 1:B 2:C 3:D 4:E
		    var needPoint = 0;
		    $("input[type='text']").each(function(){
		    	var button = this;
		    	var updateData = {};
		    	var data_type = button.getAttribute("data_type");
	    		switch (data_type) {
	    		case "a":
	    			updateData['type'] = "A";
	    			updateData['apply_count'] = Number(button.value);
	    			needPoint = needPoint + 20*Number(button.value);
	    			if(Number(button.value) > 0){
	    				updateList.push(updateData);
	    			}
	    			break;
	    		case "b":
	    			updateData['type'] = "B";
	    			updateData['apply_count'] = Number(button.value);
	    			var color_type = button.getAttribute("color_type");
	    			updateData['color'] = color_type;
	    			needPoint = needPoint + 15*Number(button.value);
	    			if(Number(button.value) > 0){
	    				updateList.push(updateData);
	    			}
	    			break;
	    		case "c":
	    			updateData['type'] = "C";
	    			updateData['apply_count'] = Number(button.value);
	    			needPoint = needPoint + 10*Number(button.value);
	    			if(Number(button.value) > 0){
	    				updateList.push(updateData);
	    			}
	    			break;
	    		case "d":
	    			updateData['type'] = "D";
	    			updateData['apply_count'] = Number(button.value);
	    			needPoint = needPoint + 2*Number(button.value);
	    			if(Number(button.value) > 0){
	    				updateList.push(updateData);
	    			}
	    			break;
	    		case "e":
	    			updateData['type'] = "E";
	    			updateData['apply_count'] = Number(button.value);
	    			needPoint = needPoint + 1*Number(button.value);
	    			if(Number(button.value) > 0){
	    				updateList.push(updateData);
	    			}
	    			break;
	    		default:
	    			break;
	    		}
	   });
	   if(checkDate(needPoint,nowPoint)){
		    	addAndUpdate(updateList);
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
function checkDate(needPoint,nowPoint){
	var flag = true;
	hidePop();
	//
	if(needPoint == 0){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0012', "");
        showPop(checkErrorMessage);
		return false;
	}
	if(nowPoint < needPoint){
		changeUserflag = true;
		checkErrorMessage = getMessage('Message.I0011', "");
		showPop(checkErrorMessage);
		return false;
	}
	
	return flag;
}

/**
 * 変更する
 * @returns
 */
function addAndUpdate(updateList){
    console.log("---------addAndUpdate---------",updateList)
    var postDataStr = JSON.stringify(updateList);
     $.ajax({
         type: 'POST',
         url: '../../../application/campaign/campaign1.php',
         data: {
             "CSRF_MIDDLE_TOKEN": $("#csrfmiddlewaretoken").val(),
             "operate": "add",
             "data": postDataStr
         },
         async: true,
         dataType: "json",
         error: function (jqXHR, textStatus, errorThrown) {
        	 alert(textStatus);
         },
         success: function (data) {
             console.log(data);
             if(data.status != 1){
				 if(data.status == 403){
					 window.location.replace("../Login.php");
					 return;
				 }
                 showPop(data.message);
                 return;
             }  else{
            	 changeUserflag = true;
            	 window.location.href="./campaign2.php"; 
             }
         }
     });
}
function searchData(){
    $.ajax({
        type: 'POST',
        url: '../../../application/campaign/campaign1.php',
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
				if(data.status == 403){
					window.location.replace("../Login.php");
					return;
				}
				showPop(data.message);
                return;
            } else{
            	if(data.responseData != null){
            		showData(data.responseData);
            	}
            }
        }
    });
}
function showData(data){
  	 $("#nowPoint").text(data.point);
}
function buttonChange(button){
	//console.log(button);
	var data_type = button.getAttribute("data_type");
	var inputId = "";
	var count = 0;
	var buttonValue = "";
	if(data_type == "b"){
		var colorType = button.getAttribute("color_type");
		inputId = "#"+data_type+"_count"+colorType;
		count = Number($(inputId).val());
		buttonValue = button.value;
	}else{
		inputId = "#"+data_type+"_count";
		count = Number($(inputId).val());
		buttonValue = button.value;
	}
	if(buttonValue == "＋"){
		count = count + 1;
	}else if(buttonValue == "－"){
		if(count > 0){
			count = count - 1;
		}
	}
	$(inputId).val(count);
}
function hidePop(){
	$('.error').hide();
	$("#checkErrortext").html("");
}
function showPop(str){
	$('.error').show();
	$("#checkErrortext").html(str);
}