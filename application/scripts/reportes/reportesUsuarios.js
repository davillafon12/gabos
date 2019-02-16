$(document).ready(function (){
	//Utilitarios.fnInicializarCalendario("#fecha_inicial", true, Utilitarios.fnObtenerFechaAnterior(1));
	Utilitarios.fnInicializarCalendario("#fecha_inicial", true, new Date());
	Utilitarios.fnInicializarCalendario("#fecha_final", true, new Date());
	$("#submit").attr("disabled", true);
	eventoTipoReporte();	
	$(".sucDesamparados").hide();
	$("#sucursal").change(function(){
		fnVerificarEmpresa();
	});
	Utilitarios.lfEventoCheckbox($("#mFecha"), $(".rFechas")); 
	$.validator.addMethod("MayorOIgualQue", function (value, element, params) {
        return Utilitarios.fnVerificarFechaMayor(params, value);
    }, '.   Debe ser mayor a: Fecha');
    lpvalidarCampos();	
}); 

onload = function () {
	var e = document.getElementById("refreshed");
	if (e.value == "no")
		e.value = "yes";
	else {
		e.value = "no";
		location.reload();
	}
}

function eventoTipoReporte(){	
	$("#tipo_reporte").change(function(){
		fnVerificarEmpresa();
		if($("#tipo_reporte").val() == 'null'){	
			$("#submit").attr("disabled", true); 
			ocultarTodo();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_ListaUsuario){
			$("#submit").attr("disabled", false);
			$(".rFechas").hide();
			$(".uFacturas").hide();
			$(".fFechas").show();
			$("#mFecha").attr("checked", false);
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_ListaDefacturasPorUsuario){
			$(".fFechas").hide();
			$(".rFechas").show();
			$(".uFacturas").show();
			$("#mFecha").attr("checked", false);
			$("#submit").attr("disabled", false);
			$("#paEsSucursal").attr("checked", false);
			$("#paEstadoFactura").val("cobrada");
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_ListaDefacturasPorUsuarioResumido){
			$(".fFechas").hide();
			$(".rFechas").show();
			$(".uFacturas").show();
			$("#mFecha").attr("checked", false);
			$("#submit").attr("disabled", false);
			$("#paEsSucursal").attr("checked", false);
			$("#paEstadoFactura").val("cobrada");
		}
		
	}); 
}

function fnVerificarEmpresa(){
	if ((($("#sucursal").val() == Utilitarios.fnGarotas) && ($("#tipo_reporte").val() == Utilitarios.paReporte_ListaDefacturasPorUsuario)) || 
	(($("#sucursal").val() == Utilitarios.fnGarotas) && ($("#tipo_reporte").val() == Utilitarios.paReporte_ListaDefacturasPorUsuarioResumido))){
		$(".sucDesamparados").show();
	}
	else{
		$(".sucDesamparados").hide();
	}
}

function ocultarTodo(){
	$(".rFechas").hide();
	$(".uFacturas").hide();
	$(".oculto").hide();
	$(".fFechas").hide();
	$("#mFecha").attr("checked", false);
	$(".sucDesamparados").hide();
}

function lpvalidarCampos() {
    $(".reporte_usuarios_form-form").validate({
        onsubmit: false,
        rules: {
            fecha_inicial: {
                required: true,
            },
            fecha_final: {
                required: true,
                MayorOIgualQue: "#txtFechaCreacionIncidencia"
            }
        },
        messages: {
            fecha_inicial: {
                required: ""
            },
            fecha_final: {
                required: ""
            }
        }
    });
}