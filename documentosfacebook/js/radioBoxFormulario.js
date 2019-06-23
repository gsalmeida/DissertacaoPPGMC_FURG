$(document).ready(function() {
	
	$("#radioNegative").click(function() {
		document.getElementById('radioNeutral').checked = false;
		document.getElementById('radioPositive').checked = false;
	});
	$("#radioNeutral").click(function() {
		document.getElementById('radioNegative').checked = false;
		document.getElementById('radioPositive').checked = false;
	});
	$("#radioPositive").click(function() {
		document.getElementById('radioNegative').checked = false;	
		document.getElementById('radioNeutral').checked = false;
	});
	
});