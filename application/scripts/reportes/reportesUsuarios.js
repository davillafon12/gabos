$(document).ready(function (){
	//Utilitarios.fnInicializarCalendario("#fecha_inicial", true, Utilitarios.fnObtenerFechaAnterior(1));
	Utilitarios.fnInicializarCalendario("#fecha_inicial", true, new Date());
	Utilitarios.fnInicializarCalendario("#fecha_final", true, new Date());
	$("#submit").attr("disabled", true);
	eventoTipoReporte();	
	eventoCheckbox();
	$.validator.addMethod("MayorOIgualQue", function (value, element, params) {
        return Utilitarios.fnVerificarFechaMayor(params, value);
    }, '.   Debe ser mayor a: Fecha');
    lpvalidarCampos();	
}); 

function eventoTipoReporte(){	
	$("#tipo_reporte").change(function(){
		if($("#tipo_reporte").val() == 'null'){	
			$("#submit").attr("disabled", true); 
			ocultarTodo();
		}
		if($("#tipo_reporte").val() == 'ListaUsuario'){
			$("#submit").attr("disabled", false);
			$(".rFechas").hide();
			$(".uFacturas").hide();
			$(".fFechas").show();
		}
		if($("#tipo_reporte").val() == 'ListaDefacturasPorUsuario'){
			$(".fFechas").hide();
			$(".rFechas").show();
			$(".uFacturas").show();
			$("#mFecha").attr("checked", false);
			$("#submit").attr("disabled", false);
		}
	}); 
}

function eventoCheckbox(){
	$("#mFecha").change(function(){
		if($(this).is(":checked")){
			$(".rFechas").show();
		}else{
			$(".rFechas").hide();
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