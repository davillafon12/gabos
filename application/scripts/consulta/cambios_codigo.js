var consecutivoActual = 0; // Guarda el consecutivo de la factura con la que se esta trabajando
var tipo_moneda = 'colones'; //Guarda el tipo de moneda que se esta cargando en la factura
var iva_por = 0; // Guarda el porcentaje del iva
var tipo_cambio = 1; //Guarda el tipo de cambio
var tipo_factura = ''; //Guarda el tipo de factura, ya que si es factura pendiente no la imprime
var sucursal = 0;
var servidorImpresion = 0;
var token = '';

var consecutivoActual = 0; // Guarda el consecutivo de la factura con la que se esta trabajando
var tipo_moneda = 'colones'; //Guarda el tipo de moneda que se esta cargando en la factura
var iva_por = 0; // Guarda el porcentaje del iva
var tipo_cambio = 1; //Guarda el tipo de cambio
var tipo_factura = ''; //Guarda el tipo de factura, ya que si es factura pendiente no la imprime
var sucursal = 0;
var servidorImpresion = 0;
var token = '';

$(function() {
	 $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
	$( "#fecha_desde" ).datepicker();
	$( "#fecha_hasta" ).datepicker();
	
	
	$("#consecutivo").numeric()
});

function validarFechas(){
	desde = $("#fecha_desde").val();
	hasta = $("#fecha_hasta").val();
	patt = /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[012])[\/]\d{4}$/;
	if(desde.trim()!=''){
		//Validamos
		if(!patt.test(desde.trim())){
			notyMsg('La fecha -desde- no tiene un formato válido', 'error');
			return false;
		}
		
		
	}
	if(hasta.trim()!=''){
		//Validamos
		if(!patt.test(hasta.trim())){
			notyMsg('La fecha -hasta- no tiene un formato válido', 'error');
			return false;
		}
		
	}
	
	if(desde.trim()!=''&&hasta.trim()!=''){
		//Pasar de dd/mm/yyyy a yyyy-mm-dd
		desde = desde.split("/");	
		desde = desde[2]+"-"+desde[1]+"-"+desde[0];
		desde = new Date(desde);
		//Pasar de dd/mm/yyyy a yyyy-mm-dd
		hasta = hasta.split("/");	
		hasta = hasta[2]+"-"+hasta[1]+"-"+hasta[0];
		hasta = new Date(hasta);
		
		if(desde>hasta){
			notyMsg('La fecha -desde- debe ser menor a la fecha -hasta-', 'error');
			return false;
		}
		
	}	
	return true;
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function llamarCambios(){
	if(validarFechas()){		
		$.ajax({
			url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/getCambiosCodigoFiltrados',
			type: "POST",	
			data: {'desde':$("#fecha_desde").val(),'hasta':$("#fecha_hasta").val(), 'sucursal':$("#sucursal").val()},				
			success: function(data, textStatus, jqXHR)
			{
				try{
					informacion = $.parseJSON('[' + data.trim() + ']');
					if(informacion[0].status==="error"){
						manejarErrores(informacion[0].error);
					}else if(informacion[0].status==="success"){
						montarCambios(informacion[0].cambios);
					}
				}catch(e){
					notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{}
		});
	}
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyMsg('No se puedo realizar la búsqueda, por favor contacte al administrador', 'error');
		break;
		case '2':
			notyMsg('No URL tiene un formato inválido, por favor contacte al administrador', 'error');
		break;
		case '3':
			notyMsg('No hay cambios de código con los filtros ingresados', 'warning');
			$("#facturas_filtradas").html('');
		break;
		case '4':
			notyMsg('Alguna de las fechas ingresadas no tiene un formato válido', 'error');
		break;
		case '5':
			notyMsg('La sucursal ingresada no existe', 'error');
		break;
	}
}

function montarCambios(cambios){
	cuerpoTabla = '';
	for(i = 0; i<cambios.length; i++){
		cuerpoTabla += "<tr class='bordes_tabla_factura' onclick='seleccionarCambio("+cambios[i].consecutivo+")'><td class='contact' style='text-align:center;'>"+cambios[i].consecutivo+"</td><td class='contact'>"+cambios[i].nombre+" "+cambios[i].apellidos+"</td><td class='contact' style='text-align:center;'>"+cambios[i].fecha+"</td></tr>";
	}
	$("#facturas_filtradas").html(cuerpoTabla);
}

function seleccionarCambio(cambio){
	$("#consecutivo").val(cambio);
	cargarCambio();
}

function cargarCambio(){
	consecutivo = $("#consecutivo").val();
	if(consecutivo.trim()===''){
		notyMsg('Debe ingresar un consecutivo válido', 'error');
		return false;
	}
	cargarInfoCambio(consecutivo);
}

function cargarInfoCambio(consecutivo){
	consecutivoActual = consecutivo;
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/consulta/getCambioCodigo",
		type: "POST",
		data: {'cambio':consecutivo,'sucursal':$("#sucursal").val()},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaHEAD = $.parseJSON('[' + data.trim() + ']');
				if(facturaHEAD[0].status==="error"){
					notyMsg("Error al cargar el cambio de código, contacte al administrador. ERROR E"+facturaHEAD[0].error, "error");
					cleanTable();
				}else if(facturaHEAD[0].status==="success"){
					setProductosCambio(facturaHEAD[0].cambioBody);
					
					sucursal = facturaHEAD[0].sucursal;
					servidorImpresion = facturaHEAD[0].servidor_impresion;
					token = facturaHEAD[0].token;
				}
			}
			catch(e){
				alert(e);
				notyMsg("Error al cargar el cambio de código, contacte al administrador. ERROR E0", "error");
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function cleanTable(){
	elmtTable = document.getElementById('tabla_productos');
	tableRows = elmtTable.getElementsByTagName('tr');
	rowCount = tableRows.length;

	for (var x=rowCount-1; x>0; x--) {
	   elmtTable.removeChild(tableRows[x]);
	}
}

function setProductosCambio(cambios){	
	
	$("#contenidoArticulos").html('');	
	cantidad = cambios.length;
	for (var i = 0; i < cantidad; i++) 
	{
		fila = "<tr>";
		fila += "<td><label class='contact'>"+cambios[i].Articulo_Cambio+"</label></td>";
		fila += "<td><label class='contact'>"+cambios[i].Descripcion_Cambio+"</label></td>";
		fila += "<td><img src='"+location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/application/images/recibos/flecha_derecha.png'/></td>";
		fila += "<td><label class='contact'>"+cambios[i].Articulo_Abonado+"</label></td>";
		fila += "<td><label class='contact'>"+cambios[i].Descripcion_Abonado+"</label></td>";
		fila += "<td><label class='contact'>"+cambios[i].Cantidad+"</label></td>";
		fila += "</tr>";
		
		$("#contenidoArticulos").append(fila);
	}
}

// Para el tamaño del windows open
var anchoImpresion = 1024;
var alturaImpresion = 768;
var tipoImpresion = 'c';

function cambiarTipoImpresion(tipo){
	tipoImpresion = tipo;
	switch(tipo){
		case 't':
			anchoImpresion = 290;
			alturaImpresion = 400;
		break;
		case 'c':
			anchoImpresion = 1024;
			alturaImpresion = 768;
		break;
	}
}

function imprimir(){
	consecutivo = $("#consecutivo").val();
	if(consecutivo.trim()===''){
		notyMsg('Debe ingresar un consecutivo válido', 'error');
		return false;
	}else if(consecutivo!=consecutivoActual){
		notyMsg('El consecutivo ingresado no coincide con el cargado', 'error');
		return false;
	}
	
	if(tipoImpresion==='t'){
		//Impresion termica
		window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion/termica?t='+token+'&d=cdc&n='+consecutivoActual+'&s='+sucursal+'&i='+tipoImpresion+'&server='+document.domain+'&protocol='+location.protocol,'Impresion de Cambios de Código','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
	}else if(tipoImpresion==='c'){
		//Impresion carta
		window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+token+'&d=cdc&n='+consecutivoActual+'&s='+sucursal+'&i='+tipoImpresion,'Impresion de Cambios de Código','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
	}
}