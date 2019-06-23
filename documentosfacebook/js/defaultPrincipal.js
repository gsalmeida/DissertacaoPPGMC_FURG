$(document).ready(function() {
	
	$('#formulario').submit(function(){
		var dados = jQuery(this).serialize(); // var dados = jQuery("#formulario").serialize();
		
		document.getElementById("box-radio-polaridade").style.color = "#555555";
		document.getElementById("box-radio-polaridade").style.border = "none";
		
		var negative = null;
		var neutral = null;
		var positive = null;
		negative = document.getElementById("radioNegative").checked;
		neutral = document.getElementById("radioNeutral").checked;
		positive = document.getElementById("radioPositive").checked;
		
		var qtdeSelecionados = 0;
		$("#ListaPresenteSelect option:selected").each(function () {
			qtdeSelecionados++;
		});
		
		if( (!negative) && (!neutral) && (!positive) && (qtdeSelecionados) ) {
			document.getElementById("box-radio-polaridade").style.color = "red";
			document.getElementById("box-radio-polaridade").style.border = "solid 1px";
			return false;
		}
		
		document.getElementById('botao-enviar-proximo-principal').disabled = true;
		jQuery.ajax({
			type: "POST",
			url: "cadastrarClassificacao.php",
			data: dados,
			success: function( data ) {
				$(".recebeDadosAjax").html(data);
			}
		});
		return false;
	});
	
});
