$(document).ready(function (){
	//Utilitarios.fnInicializarCalendario("#fecha_inicial", true, Utilitarios.fnObtenerFechaAnterior(1));
	Utilitarios.fnInicializarCalendario("#fecha_inicial", true, new Date());
	Utilitarios.fnInicializarCalendario("#fecha_final", true, new Date());
	$("#submit").attr("disabled", true);
	eventoTipoReporte();	
	//Utilitarios.lfEventoCheckbox($("#mFecha"), $(".rFechas"));
	
});

function eventoTipoReporte(){	
	$("#tipo_reporte").change(function(){
		if($("#tipo_reporte").val() == 'null'){	
			$("#submit").attr("disabled", true); 
			ocultarTodo();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_RentabilidadXCliente){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").show();
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_VentasXMes){
			$("#submit").attr("disabled", false);
			$(".rFechas").show();
			$(".uFacturas").show();
		}
	}); 
}

function ocultarTodo(){
	$(".rFechas").hide();
	$(".uFacturas").hide();
	$(".oculto").hide();
	$(".fFechas").hide();
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