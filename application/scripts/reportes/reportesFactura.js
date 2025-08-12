$(document).ready(function (){
	//Utilitarios.fnInicializarCalendario("#fecha_inicial", true, Utilitarios.fnObtenerFechaAnterior(1));
	Utilitarios.fnInicializarCalendario("#fecha_inicial", true, new Date());
	Utilitarios.fnInicializarCalendario("#fecha_final", true, new Date());
	$("#submit").attr("disabled", true);
	eventoTipoReporte();	
	//Utilitarios.lfEventoCheckbox($("#mFecha"), $(".rFechas"));
	$(".sucDesamparados").hide();
	$("#sucursal").change(function(){
		fnVerificarEmpresa();
	});
	Utilitarios.lfEventoCheckbox($("#mNombre"), $(".fNombre")); 
	Utilitarios.lfEventoCheckbox($("#mCedula"), $(".fCedula")); 
});

function eventoTipoReporte(){	
	$("#tipo_reporte").change(function(){
		fnVerificarEmpresa();
		if($("#tipo_reporte").val() == 'null'){	
			$("#submit").attr("disabled", true); 
			ocultarTodo();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_RentabilidadXCliente){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").show();
			$(".mNombre").hide();
			$(".mCedula").hide();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_VentasXMes){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").show();
			$(".mNombre").hide();
			$(".mCedula").hide();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_RecibosXDinero){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").hide();
			$(".mNombre").show();
			$(".mCedula").show();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_NotaCredito){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").hide();
			$(".mNombre").show();
			$(".mCedula").hide();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_Cartera || $("#tipo_reporte").val() == Utilitarios.paReporte_CarteleraTotalizacion){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").hide();
			$(".mNombre").show();
			$(".mCedula").hide();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_ArticulosExentos){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").hide();
			$(".mNombre").hide();
			$(".mCedula").hide();
		}
	}); 
}

function fnVerificarEmpresa(){
	if ((($("#sucursal").val() == Utilitarios.fnGarotas) && ($("#tipo_reporte").val() == Utilitarios.paReporte_RentabilidadXCliente))
		||(($("#sucursal").val() == Utilitarios.fnGarotas) && ($("#tipo_reporte").val() == Utilitarios.paReporte_VentasXMes))
		||(($("#sucursal").val() == Utilitarios.fnGarotas) && ($("#tipo_reporte").val() == Utilitarios.paReporte_NotaCredito)) ){
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
	$(".mNombre").hide();
	$(".sucDesamparados").hide();
	$(".mCedula").hide();
	$("#mFecha").attr("checked", false);
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