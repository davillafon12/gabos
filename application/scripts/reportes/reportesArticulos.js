$(document).ready(function (){
	ocultartodo();
	Utilitarios.lfComboRangos($("#rangoCodigo"), $(".rCodigoI"), $(".rCodigoF"));
	//ComboRangoCodigo();
	
}); 


function ocultartodo(){
	$(".rCodigoI").hide(); 
	$(".rCodigoF").hide(); 	
	$(".rPrecioI").hide(); 
	$(".rPrecioF").hide(); 
	$(".rArticulosI").hide(); 
	$(".rArticulosF").hide(); 
	$(".rArticulosDefI").hide(); 
	$(".rArticulosDefF").hide(); 
}

function ComboRangoCodigo(){
	$("#rangoCodigo").change(function(){
		if($("#rangoCodigo").val()!= "null"){
			if($("#rangoCodigo").val()!=Utilitarios.paMenorIgual ||
			   $("#rangoCodigo").val()!=Utilitarios.paMayorIgual){
				$(".rCodigoI").show(); 
				$(".rCodigoF").hide(); 	
			}else{
				$(".rCodigoI").show(); 
				$(".rCodigoF").show(); 
			}
		}
		else{
			$(".rCodigoI").hide(); 
			$(".rCodigoF").hide(); 
		}
	});	
}