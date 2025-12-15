function EnterLogin(e){
	if(e.keyCode == 13){ 
		login();
	}
}

function login(){
	name = $('#loginUser').val();
	pass = $('#loginPass').val();

	if(name=="" && pass==''){
		$('#divError').toggleClass('shake').html("All fields are mandatory.");
		setTimeout("$('#divError').toggleClass('shake')",1000);
		$('#loginUser').focus();
		return false;
	}else if(pass=="" && name!=""){
		$('#divError').toggleClass('shake').html("Please enter a password.");
		setTimeout("$('#divError').toggleClass('shake')",1000);
		$('#loginPass').focus();
		return false;
	}else if(pass!="" && name==""){
		$('#divError').toggleClass('shake').html("Please enter a username.");
		setTimeout("$('#divError').toggleClass('shake')",1000);
		$('#loginUser').focus();
		return false;
	}else if(name!='' && pass!=''){
		var url = root+'login/';
		$.post(url,{
				user: name,
				pass: pass,
				csrf_token: token_value
			},
			function(data){
				if(data==1){
					$('#divError').html('');
					location.href = root;
				}else{
					token_value = data;
					$('#divError').toggleClass('shake').html("The username or password is incorrect.");
					setTimeout("$('#divError').toggleClass('shake')",1000);
				}
			}
		);
	}
}

function reset(){
	$('#loginUser').val('');
	$('#loginPass').val('');
}

$(document).ready(function(){
	$('#loginUser').focus();
	_resize();
});

$( window ).resize(function() {
	_resize();
});

function _resize(){
	var h = $(document).height();
	var top_form = ((h-$('.bg_login').height())/2)-$('.logo').height();
	var top_logo = top_form/2;
	top_form = top_form - top_logo;
	$('.logo').css('margin-top',top_logo);
	$('.bg_login').css('margin-top',top_form);
}