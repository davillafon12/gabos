$(document).ready(function (){
	//Utilitarios.fnInicializarCalendario("#fecha_inicial", true, Utilitarios.fnObtenerFechaAnterior(1));
	Utilitarios.fnInicializarCalendario("#fecha_inicial", true, new Date());
	Utilitarios.fnInicializarCalendario("#fecha_final", true, new Date());
	$("#submit").attr("disabled", true);
	eventoTipoReporte();	
	eventoRangos();
	Utilitarios.lfEventoCheckbox($("#mNombre"), $(".fNombre")); 
	Utilitarios.lfEventoCheckbox($("#mCedula"), $(".fCedula")); 
	Utilitarios.lfEventoCheckbox($("#mRango"), $(".fRango")); 
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
		if($("#tipo_reporte").val() == Utilitarios.paReporte_VentaXClienteFacturas){
			$(".rFechas").show();
			$(".uFacturas").show();
			$(".mFiltro").show();
			$(".mNombre").show();
			$(".mCedula").show();
			$(".mRango").show();
			$(".mEstado").hide();
			$(".mSucursal").show();
			$("#mFecha").attr("checked", false);
			$("#submit").attr("disabled", false);
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_VentaXClienteProforma){
			$(".rFechas").show();
			$(".uFacturas").show();
			$(".mFiltro").show();
			$(".mNombre").show();
			$(".mCedula").show();
			$(".mRango").show();
			$(".mEstado").hide();
			$(".mSucursal").show();
			$("#mFecha").attr("checked", false);
			$("#submit").attr("disabled", false);
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_VentaXClienteFacturasResumen){
			$(".rFechas").show();
			$(".uFacturas").show();
			$(".mFiltro").show();
			$(".mNombre").show();
			$(".mCedula").show();
			$(".mRango").show();
			$(".mEstado").hide();
			$(".mSucursal").show();
			$("#mFecha").attr("checked", false);
			$("#submit").attr("disabled", false);
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_ClienteEstado){
			$("#submit").attr("disabled", false); 
			ocultarTodo();
			$(".mEstado").show();			
		}
		if($("#tipo_reporte").val() == Utilitarios.paReporte_ClientesXDescuento){
			$("#submit").attr("disabled", false); 			
			ocultarTodo();	
			$(".mCedula").show();
			$(".mSucursal").show();
			$(".mArticulo").show();
			$(".mFamilia").show();
			$(".mFiltro").show();
						
		}
	}); 
}

function ocultarTodo(){
	$(".rFechas").hide();
	$(".uFacturas").hide();
	$(".oculto").hide();
	$(".fFechas").hide();
	$(".mNombre").hide();
	$(".fNombre").hide();
	$(".mCedula").hide();
	$(".fCedula").hide();
	$(".fRango").hide();
	$(".mRango").hide();
	$(".fRango1").hide();
	$(".fRango2").hide();
	$(".mEstado").hide();
	$(".mSucursal").hide();
	$(".mFiltro").hide();
	$(".mFamilia").hide();
	$(".mFiltro").hide();
	$("#mFecha").attr("checked", false);
}

function eventoRangos(){
	$("#rangoM").change(function(){
		if($("#rangoM").val() == 'null'){	
			$(".fRango1").hide();
			$(".fRango2").hide();
		}
		if($("#rangoM").val() == 'menorIgual'){	
			$(".fRango1").show();
			$(".fRango2").hide();
		}
		if($("#rangoM").val() == 'mayorIgual'){	
			$(".fRango1").show();
			$(".fRango2").hide();
		}
		if($("#rangoM").val() == 'between'){	
			$(".fRango1").show();
			$(".fRango2").show();
		}
		
	}); 	
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
