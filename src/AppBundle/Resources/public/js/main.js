$(document).ready(function() {
	$('form').each(function() {
		$(this).parsley();

		/*----------------------------------------------------*/
		/*  Add/Remove people - Team field
		/*----------------------------------------------------*/
		if($(this).attr('id') == 'addTeamMemberForm') {
			$(this).submit(function(e) {
				e.preventDefault();

				// add member
				var num = $('.list-team li').length;
				var name = $('#memberName').val();
				var desc = $('#descriptionMember').val();
				var panel = '<li id="mem' + num + '"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><span class="accordian-caret"><a data-toggle="collapse" data-parent="#accordion" href="#collapse' + num + '">' + name + '</a></span></h4></div><!-- End of panel heading --><div id="collapse' + num + '" class="panel-collapse collapse"><div class="panel-body">' + desc + '</div><!-- End of panel body --></div><!-- End of panel collapse --></div><!-- End of panel --><i class="glyphicon glyphicon-remove" onclick="javascript:removeMember(\'mem' + num + '\')"></i></li>';
				
				$('.list-team').append(panel);

				// remove value
				$('#memberName, #descriptionMember').val('');
				validationTeam();

				// close modal
				$('#modal-addTeamMember').modal('hide');
			});
		}
	});
	
	if($(window).width() >= 768){
		$('img').each(function() {
			if($(this).hasClass('img-rounded-small')) {
				var h, w;
				loadImage($(this).attr('src'), function(img) {
					w = img.width;
					h = img.height;
				});
				if(w > h) {
					$(this).css('height', '235px');
				} else {
					$(this).css('width', '360px');
				}
				console.log(w + '-' + h);
			}
		});
	}

	// sign up
	/*$("#signUpBtn").click(function(event) {
		event.preventDefault();
		validate = validateSignUpForm();
		if (validate == true)
			SignUp($(this));
	});*/
	// end sign up
	// sign in
	/*$("#signInBtn").click(function(event) {
		event.preventDefault();
		validate = validateSignInForm();
		if (validate == true)
			SignIn($(this));
	});*/
	// end sign in
});
function loadImage(imgSrc, callback){
  var image = new Image();
  image.src = imgSrc;
  if (image.complete) {
    callback(image);
    image.onload=function(){};
  } else {
    image.onload = function() {
      callback(image);
      // clear onLoad, IE behaves erratically with animated gifs otherwise
      image.onload=function(){};
    }
    image.onerror = function() {
        alert("Could not load image.");
    }
  }
}
function removeMember(id) {
	jQuery('#' + id).remove();
	validationTeam();
}
function validationTeam() {
	if(jQuery('.list-team li').length > 0) {
		jQuery('#team').val('added');
	} else {
		jQuery('#team').val('');
	}
}
/*function validateSignUpForm(){
	$selector =  $("#signUpForm");
	var $messages = '{"email":"Email ", ' +
					'"accountPassword":"Password ", ' +
					'"confirmPassword":"Password "}';
	var $rules = 
			'{"fname":{"text": true},' +
			'"lname":{"text": true},' +
			'"email":{"email": true},' +
			'"accountPassword":{"minlength":6},' +
			'"confirmPassword":{"equalTo":"accountPassword"}}';
			
	return validateForm($selector, $rules, $messages);
}

function SignUp(thisObj) {
	var form = $("#signUpForm");
	var $this = thisObj;
	var alreadyClicked = $this.data('clicked');
	if (alreadyClicked)
		return false;
	$this.data('clicked', true);
	$('#modal-signup .status').removeClass('error');
	$('#modal-signup .status').html('Signing Up...');
	$.ajax({
		url : form.attr("action"),
		type: "POST",
		data : form.serialize(),
		success: function(data, textStatus, jqXHR){//console.log(data);
			$('#modal-signup .status').html('');
			if (data.status == 0) {
				window.location.href = data.url;
			} else {
				$('#modal-signup .status').addClass('error');
				if (data.status == 1) {
					$('#modal-signup .status').html("Password should have atleast 6 characters.");
				} else if (data.status == 2){
					$('#modal-signup .status').html("You already have an account.");
				} else if (data.status == 3){
					$('#modal-signup .status').html("Password and Confirm Password should be same.");
				} else {
					$('#modal-signup .status').html('Internal server error');
				}
				$this.data('clicked', false);
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
		}
	});
}

function validateSignInForm(){
	$selector =  $("#signInForm");
	var $messages = '{"loginPassword":"Password ", ' +
					'"loginEmail":"Email "}';
	var $rules = 
			'{"loginPassword":{"text": true},' +
			'"loginEmail":{"email": true}}';
			
	return validateForm($selector, $rules, $messages);
}

function SignIn(thisObj) {
	var form = $("#signInForm");
	var $this = thisObj;
	var alreadyClicked = $this.data('clicked');
	if (alreadyClicked)
		return false;
	$this.data('clicked', true);
	$('#modal-signin .status').removeClass('error');
	$('#modal-signin .status').html('Logging In...');
	$.ajax({
		url : form.attr("action"),
		type: "POST",
		data : form.serialize(),
		success: function(data, textStatus, jqXHR){//console.log(data.url);
			$('#modal-signin .status').html('');
			if (data.status == 0) {
				window.location.href = data.url;
			} else {
				$('#modal-signin .status').addClass('error');
				if (data.status == 1) {
					$('#modal-signin .status').html("The email/password provided are not valid.");
				} else if (data.status == 2){
					$('#modal-signin .status').html("Sorry! You can not access this network.");
				} else {
					$('#modal-signin .status').html('Internal server error');
				}
				$this.data('clicked', false);
			}
		},
		error: function (jqXHR, textStatus, errorThrown){
		}
	});
}*/