$(document).ready(function() {
	// update profile
	$("#btn_update_profile1").click(function(event) {
		event.preventDefault();
		updateProfileAjax($(this));
	});
	$("#btn_update_profile2").click(function(event) {
		event.preventDefault();
		validate = validateProfile2Ajax();
		if(validate == true){
			updateProfile2Ajax($(this));
		}
	});
	// end update profile
	
	// update password
	$("#btn_update_password").click(function(e){
		e.preventDefault();
		validate = validateFormChangePassword();
		if(validate == true){
			updatePassword($(this));
		}
	});
	// end update password
});

function updateProfileAjax(thisObj) {
	var form = $("#update_profile1");
	var $this = thisObj;
	var alreadyClicked = $this.data('clicked');
	if (alreadyClicked)
		return false;
	$this.data('clicked', true);
	$('#editTab .status').html('Updating...').css("margin-left", "20px");
	$.ajax({
		url : form.attr("action"),
		type: "POST",
		data : form.serialize(),
		success: function(response, textStatus, jqXHR){//console.log(data.url);
			$('#editTab .status').html('');
			if (response.status == 0) {
				$this.data('clicked', false);
				$("#bio_info").text($("#bio").val());
				$("#facebook_info").text($("#facebook").val());
				$("#investment_preferences_info").text($("#investment_preferences").val());
				$("#overview-tab").click();
				$("#edit-tab").parent().removeClass('active');
			} else {
				$this.data('clicked', false);
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
		}
	});
}

function validateProfile2Ajax(){
	var $selector =  $("#update_profile2");
	var $messages = '{"fname":"First Name ",' +
					'"lname":"Last Name "}';
	var $rules = '{"fname":{"text": true},' +
				'"lname":{"text": true}}';

	return validateForm($selector, $rules, $messages);

}

function updateProfile2Ajax(thisObj) {
	var form = $("#update_profile2");
	var $this = thisObj;
	var alreadyClicked = $this.data('clicked');
	if (alreadyClicked)
		return false;
	$this.data('clicked', true);
	$('#nameTab .status').html('Updating...').css("margin-left", "20px");
	$.ajax({
		url : form.attr("action"),
		type: "POST",
		data : form.serialize(),
		success: function(response, textStatus, jqXHR){//console.log(data.url);
			$('#nameTab .status').html('');
			if (response.status == 0) {
				$this.data('clicked', false);
				$("#person_name").text($("#fname").val()+' '+$("#lname").val());
				$("#overview-tab").click();
				$("#name-tab").parent().removeClass('active');
			} else {
				$this.data('clicked', false);
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
		}
	});
}

function validateFormChangePassword(){
	var $selector =  $("#update_password");
	var $messages = '{"new_password":"Password ",' +
			'"new_password_again":"Password "}';
	var $rules = '{"current_password":{"text": true},' +
			'"new_password":{"minlength":6},' +
			'"new_password_again":{"equalTo":"new_password"}}';

	return validateForm($selector, $rules, $messages);

}

function updatePassword(thisObj) {
	var form = $("#update_password");
	var $this = thisObj;
	var alreadyClicked = $this.data('clicked');console.log(alreadyClicked);
	if (alreadyClicked)
		return false;
	$this.data('clicked', true);
	$('#passTab .status').html('Updating...').css("margin-left", "20px");
	$.ajax({
		url : form.attr("action"),
		type: "POST",
		data : form.serialize(),
		success: function(response, textStatus, jqXHR){
			$('#passTab .status').html('');
			if (response.status == 0)
			{
				$this.data('clicked', false);
				$("#update_password")[0].reset();
				$("#overview-tab").click();
				$("#pass-tab").parent().removeClass('active');
			} else {
				$('#passTab .status').html('Password not match.');
				$this.data('clicked', false);
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
		}
	});
}

function updateProfileImage(data, path, callback) {
	$.ajax({
		type: 'POST',
		cache: false,
		dataType: 'html',
		url: path,
		data: data,
		success: function (html, status, xhr) {
			callback(data);
		}
	});
}
		
var saveImage = function () {
	var image = $('#file_profile_image').attr('s3_url');
	var image_save_path = $('#file_profile_image').attr('image_save_path');
	updateProfileImage({'info_profile_image': image}, image_save_path, function (options) {
		location.reload();
	});
}