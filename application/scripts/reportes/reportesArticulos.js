$(document).ready(function (){
	ocultartodo();
	Utilitarios.lfComboRangos($("#rangoCodigo"), $(".rCodigoI"), $(".rCodigoF"));
	Utilitarios.lfComboRangos($("#rangoPrecios"), $(".rPrecioI"), $(".rPrecioF"));
	Utilitarios.lfComboRangos($("#rangoArticulos"), $(".rArticulosI"), $(".rArticulosF"));
	Utilitarios.lfComboRangos($("#rangoArticulosDef"), $(".rArticulosDefI"), $(".rArticulosDefF"));	
	
	Utilitarios.fnInicializarCalendario("#fecha_inicial", true, new Date());
	Utilitarios.fnInicializarCalendario("#fecha_final", true, new Date());
	mostrarRangos();
}); 

function mostrarRangos(){
	$("#tipo_reporte").change(function(){
		ocultartodo();
		fnVerificarEmpresa();
		if($("#tipo_reporte").val() == 'null'){	
			$("#submit").attr("disabled", true); 
			ocultartodo();
		}										   
		if($("#tipo_reporte").val() == Utilitarios.paReporte_InventarioArticulos){
			$("#submit").attr("disabled", false);
			$(".rArticulos").show();
		}										   
		if($("#tipo_reporte").val() == Utilitarios.paReporte_CantArtVentaCliente){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".rCodigoCedula").show();
			$(".rCodigo").show();
			$(".rCedula").show();
		}										 
		if($("#tipo_reporte").val() == Utilitarios.paReporte_ProcedenciaArticulo){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".rCodigoCedula").show();
			$(".rCodigo").show();
			$(".rCedula").hide();
		}
	}); 
	
	$("#sucursal").change(function(){
		fnVerificarEmpresa();
	});
	
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
	$(".rArticulos").hide();
	$(".sucDesamparados").hide();
	$(".rFechas").hide();
	$("#paExento").attr("checked", false);
	$(".rCodigoCedula").hide();
}

function fnVerificarEmpresa(){
	if ((($("#sucursal").val() == Utilitarios.fnGarotas) && ($("#tipo_reporte").val() == Utilitarios.paReporte_CantArtVentaCliente))){
		$(".sucDesamparados").show();
	}
	else{
		$(".sucDesamparados").hide();
	}
}