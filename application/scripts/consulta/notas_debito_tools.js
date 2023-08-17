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
	
	
	$( "#nombre" ).autocomplete({
		  source: location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getNombresClientesBusqueda',
		  minLength: 1,
		  select: function( event, ui ) {
			$("#cedula").val(ui.item.id);				  
		  }
		});
	
	$("#consecutivo").numeric()
});

function llamarFacturas(){
	if(validarFechas()){		
		$.ajax({
			url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/getNotasDebitoFiltradas',
			type: "POST",	
			data: {'desde':$("#fecha_desde").val(),'hasta':$("#fecha_hasta").val()},				
			success: function(data, textStatus, jqXHR)
			{
				try{
					informacion = $.parseJSON('[' + data.trim() + ']');
					if(informacion[0].status==="error"){
						manejarErrores(informacion[0].error);
					}else if(informacion[0].status==="success"){
						montarFacturas(informacion[0].notas);
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

function tiposSeleccionados(){
	tipos = [];
	selec = $("input[name='tipo']:checked");
	for(i=0; i<selec.length; i++){
		tipos.push({dato:selec[i].value});
	}
	return JSON.stringify(tipos);
}

function estadosSeleccionados(){
	tipos = [];
	selec = $("input[name='estado']:checked");
	for(i=0; i<selec.length; i++){
		tipos.push({dato:selec[i].value});
	}
	return JSON.stringify(tipos);
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
			notyMsg('No hay notas crédito con los filtros ingresados', 'warning');
			$("#facturas_filtradas").html('');
		break;
		case '4':
			notyMsg('Alguna de las fechas ingresadas no tiene un formato válido', 'error');
		break;
		case '5':
			notyMsg('La fecha desde debe ser menor a la fecha hasta', 'error');
		break;
	}
}

function montarFacturas(facturas){
	cuerpoTabla = '';
	for(i = 0; i<facturas.length; i++){
		cuerpoTabla += "<tr class='bordes_tabla_factura' onclick='seleccionarFactura("+facturas[i].consecutivo+")'><td class='contact' style='text-align:center;'>"+facturas[i].consecutivo+"</td><td class='contact'>"+facturas[i].cliente+"</td><td class='contact' style='text-align:center;'>"+facturas[i].fecha+"</td></tr>";
	}
	$("#facturas_filtradas").html(cuerpoTabla);
}

/**
 * Number.prototype.format(n, x, s, c)
 * 
 * param integer n: length of decimal
 * param integer x: length of whole part
 * param mixed   s: sections delimiter
 * param mixed   c: decimal delimiter
	12345678.9.format(2, 3, '.', ',');  // "12.345.678,90"
	123456.789.format(4, 4, ' ', ':');  // "12 3456:7890"
	12345678.9.format(0, 3, '-');       // "12-345-679"
 */
 
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

function seleccionarFactura(factura){
	$("#consecutivo").val(factura);
	cargarFactura();
}

function cargarFactura(){
	consecutivo = $("#consecutivo").val();
	if(consecutivo.trim()===''){
		notyMsg('Debe ingresar un consecutivo válido', 'error');
		return false;
	}
	cargaServerFactura(consecutivo);
}

function cargaServerFactura(consecutivo){
	cargarEncabezado(consecutivo);
	
	consecutivoActual = consecutivo; //Asignamos el consecutivo actual para realizar operaciones	
}

function cargarEncabezado(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/consulta/getNotaDebito",
		type: "POST",
		data: {'nota':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaHEAD = $.parseJSON('[' + data.trim() + ']');
				if(facturaHEAD[0].status==="error"){
					notyMsg("Error al cargar la factura, contacte al administrador. ERROR E"+facturaHEAD[0].error, "error");
					cleanTable();
				}else if(facturaHEAD[0].status==="success"){
					setEncabezadoFactura(facturaHEAD[0].notaHead);
					setProductosFactura(facturaHEAD[0].notaBody);
					
					tipo_factura = facturaHEAD[0].tipo; 
					sucursal = facturaHEAD[0].sucursal;
					servidorImpresion = facturaHEAD[0].servidor_impresion;
					token = facturaHEAD[0].token;
				}
			}
			catch(e){
				alert(e);
				notyMsg("Error al cargar la factura, contacte al administrador. ERROR E0", "error");
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

function setEncabezadoFactura(facturaHEAD){	
	//$("#observaciones").val(facturaHEAD[0].observaciones);
	
	//Traemos decimales
	decimales = parseInt(decimales); //Esta variable se inicializa en la vista!!
	iva_por = parseFloat(facturaHEAD[0].iva);
	tipo_cambio = parseFloat(facturaHEAD[0].tipo_cambio);
	tipo_moneda = facturaHEAD[0].moneda;
	
	//Costos totales
	total = parseFloat(facturaHEAD[0].total);
	iva = parseFloat(facturaHEAD[0].total_iva);
	costo = parseFloat(facturaHEAD[0].subtotal);
	
	if(tipo_moneda==='colones'){
		$(".tipo_moneda_display").html("₡");
	}else if(tipo_moneda==='dolares'){
		$(".tipo_moneda_display").html("$");
		total = total / tipo_cambio;
		iva = iva / tipo_cambio;
		costo = costo / tipo_cambio;
	}	
	
	$("#costo_total").val(total.format(decimales, 3, '.', ','));
	$("#iva").val(iva.format(decimales, 3, '.', ','));
	$("#costo").val(costo.format(decimales, 3, '.', ','));
	
	$("#sucursal_recibe").html(facturaHEAD[0].recibe);
	$("#sucursal_entrega").html(facturaHEAD[0].entrega);
	
}

function setProductosFactura(productos){	
	
	$("#contenidoArticulos").html('');	
	cantidad = productos.length;
	for (var i = 0; i < cantidad; i++) 
	{
		fila = "<tr>";
		fila += "<td><label class='contact'>"+productos[i].codigo+"</label></td>";
		fila += "<td><div class='contact' id='descripcion_articulo_"+(i+1)+"'>"+productos[i].descripcion+"</div>";				
		fila += "<td style='text-align: center;'><label class='contact' id='cantidad_articulo_"+(i+1)+"'>"+productos[i].cantidad+"</label></td>";	
		
		
		//Traemos decimales
		decimales = parseInt(decimales); //Esta variable se inicializa en la vista!!		
		precioUI = parseFloat(productos[i].precio);
		precioUI = precioUI.format(decimales, 3, '.', ',');
		
		fila += "<td style='text-align: right;'><label class='contact'>"+precioUI+"</label>"
				+"<input id='costo_unidad_articulo_"+(i+1)+"' type='hidden' value='"+productos[i].precio+"'></td>";				
		fila += "<td style='text-align: right;'><label class='contact' id='costo_total_articulo_"+(i+1)+"'></label></td>";
		
		fila += "</tr>";
		
		$("#contenidoArticulos").append(fila);
	}
	setCostos(cantidad);
}

function agregarTooltip(id_Row){
	$(id_Row).mouseover(function(){
		eleOffset = $(this).offset();

		$(this).next().fadeIn("fast").css({
			left: eleOffset.left + 100,
			top: eleOffset.top - 100
		});
		
	}).mouseout(function(){
		$(this).next().hide();
	});
}

function setCostos(cantidad){	
	for (var i = 0; i < cantidad; i++) 
	{	actualizaCostoTotalArticulo("cantidad_articulo_"+(i+1));
	}
}

function actualizaCostoTotalArticulo(id){
	
	num_row = id.replace("cantidad_articulo_","");
	cantidad = document.getElementById("cantidad_articulo_"+num_row).innerHTML;
	precio_unidad = document.getElementById('costo_unidad_articulo_'+num_row).value;
	
			
	//Conversion de tipos
	precio_unidad_float = parseFloat(precio_unidad);
	cantidad_float = parseFloat(cantidad);
		
	//Calculos matematicos
	precio_total = precio_unidad_float * cantidad_float;
	
	
	//Cargamos el valor al div
	costo_total = document.getElementById("costo_total_articulo_"+num_row);
	
	//Formateamos el valor		
	precio_total = precio_total.format(decimales, 3, '.', ',');	
	costo_total.innerHTML = precio_total;
	
	
}

// Para el tamaño del windows open
var anchoImpresion = 290;
var alturaImpresion = 400;
var tipoImpresion = 't';

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
	}else if(tipo_factura === 'pendiente'){
		notyMsg('No puede imprimir facturas pendientes', 'error');
		return false;
	}
	
	if(tipoImpresion==='t'){
		//Impresion termica
		window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion/termica?t='+token+'&d=nd&n='+consecutivoActual+'&s='+sucursal+'&i='+tipoImpresion+'&server='+document.domain+'&protocol='+location.protocol,'Impresion de Facturas','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
	}else if(tipoImpresion==='c'){
		//Impresion carta
		window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+token+'&d=nd&n='+consecutivoActual+'&s='+sucursal+'&i='+tipoImpresion,'Impresion de Facturas','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
	}
}

