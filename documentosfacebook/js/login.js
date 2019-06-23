$(document).ready(function() {
	
	$("#username-id").change(function() {
		if(($("#login-incorreto").html()) != "") $("#login-incorreto").html("");
	});
	
	$("#password-id").change(function() {
		if(($("#login-incorreto").html()) != "") $("#login-incorreto").html("");
	});
	
	$("#username-id-create-user").change(function() {
		if(($("#login-existente").html()) != "") $("#login-existente").html("");
	});
	
	$("#password-id-create-user").change(function() {
		if(($("#login-existente").html()) != "") $("#login-existente").html("");
	});
	
});
