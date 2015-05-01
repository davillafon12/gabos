$(document).ready(function (){
	ocultartodo();
	Utilitarios.lfComboRangos($("#rangoCodigo"), $(".rCodigoI"), $(".rCodigoF"));
	Utilitarios.lfComboRangos($("#rangoPrecios"), $(".rPrecioI"), $(".rPrecioF"));
	Utilitarios.lfComboRangos($("#rangoArticulos"), $(".rArticulosI"), $(".rArticulosF"));
	Utilitarios.lfComboRangos($("#rangoArticulosDef"), $(".rArticulosDefI"), $(".rArticulosDefF"));	
	mostrarRangos();
}); 

function mostrarRangos(){
	$("#precio").change(function(){
		if($("#precio").val() != "null"){
			$(".mRangoPrecios").show();	
			$("#temp").val("se muestra");
		}
		else{
			$("#temp").val("se oculta");
			$(".mRangoPrecios").hide();						
		}		
	});
	
}

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