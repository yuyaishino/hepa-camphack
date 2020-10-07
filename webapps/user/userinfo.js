var is_click_valid = true;

function goGrant() {
    if (is_click_valid) {
        is_click_valid = false;
        var form = document.getElementById("form1");
        form.submit();
    }
}

$(function () {
    searchData();
    $("#pword_change").click(function () {

    });
    hidePop();
});

function searchData() {
    $.ajax({
        type: 'POST',
        url: '../../../application/user/userinfo.php',
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
            if (data.status != 1) {
                if (data.status == 403) {
                    window.location.replace('../Login.php');
                    return;
                }
                showPop(data.message);
                return;
            } else {
                if (data.responseData != null) {
                    showData(data.responseData);
                }
            }
        }
    });
}

function showData(data) {
    $('#name_prifix').text(data.name_prifix + " " + data.name_suffix);
    $('#notation_prifix').text(data.notation_prifix + " " + data.notation_sufix);
    if (!isNullOrEmpty(data.birthdatetext)) {
        $('#birthdate').text(data.birthdatetext);
    } else {
        $('#birthdate').text("");
    }

    if (!isNullOrEmpty(data.postcode)) {
        $('#postcode').text(data.postcode);
    } else {
        $('#postcode').text("");
    }

    if (!isNullOrEmpty(data.address1)) {
        $('#address1').text(data.address1);
    } else {
        $('#address1').text("");
    }


    if (!isNullOrEmpty(data.address2)) {
        $('#address2').text(data.address2);
    } else {
        $('#address2').text("");
    }

    if (!isNullOrEmpty(data.address3)) {
        $('#address3').text(data.address3);
    } else {
        $('#address3').text('');
    }

    if (!isNullOrEmpty(data.tel)) {
        $('#tel').text(data.tel);
    } else {
        $('#tel').text("");
    }

    if (data.sex == 1) {
        $('#sex').text("女性");
    } 
    if (data.sex == 0) {
        $('#sex').text("男性");
    }
}

function hidePop() {
    $('.error').hide();
    $("#checkErrortext").html("");
}

function showPop(str) {
    $('.error').show();
    $("#checkErrortext").html(str);
}
	 