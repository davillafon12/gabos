function buscarCedula (e) 
{
	cedula = $("#cedula").val();
	if(!isNumber(cedula))
	{ 
		$("#nombre").val('');
		resetFields();
	}
	else
	{	
		getClienteNombreYFacturas(cedula);
	}
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function getClienteNombreYFacturas(cedula){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/notas/getFacturasCliente',
		type: "POST",		
		//async: false,
		data: {'cedula':cedula},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				//alert(data.trim());
				informacion = $.parseJSON('[' + data.trim() + ']');
				//alert(JSON.stringify(informacion[0], null, 4));
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					//alert(JSON.stringify(informacion, null, 4));
					setInformacion(informacion[0]);
				}
			}catch(e){
				//alert(e);
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyMsg('¡No se pudo tramitar la información, contacte al administrador!','error');
			resetFields();
		break;
		case '2':
			notyMsg('¡Error en el envio de la URL, contacte al administrador!','error');
			resetFields();
		break;
		case '3':
			//Cliente no existe cuando se busca por cedula
			resetFields();
			$("#nombre").val('No existe cliente!!!');
		break;
		case '4':
			//Cliente 0 o 1
			resetFields();
			$("#nombre").val('');
		break;
		case '5':
			//No hay facturas pendientes
			notyMsg('¡Este cliente no tiene facturas!','error');
			resetFields();
		break;
		case '6':
			//No se obtuvo los productos
			notyMsg('¡No se pudo obtener los productos de la factura!','error');
			resetFieldsFromProductos();
		break;		
	}
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function resetFields(){
	$("#nombre").val('');
	$("#tbody_facturas").html('');
	$("#tbody_productos").html('');
	$("#tbody_productos_seleccionados").html('');
	facturaSeleccionada = 0;
}

function setInformacion(informacion){
	$("#nombre").val(informacion.cliente.nombre+" "+informacion.cliente.apellidos);
	setFacturas(informacion.facturas);
}

function setFacturas(facturas){
	cuerpo = '';
	for(i=0; i<facturas.length; i++){
		//alert(JSON.stringify(creditos[i], null, 4));
		monto = parseFloat(facturas[i].monto);
		monto = monto.format(2, 3, '.', ',');
		cuerpo += "<tr class='bordes_tabla' onclick='seleccionarFactura("+facturas[i].consecutivo+")' id='factura_row_"+facturas[i].consecutivo+"'><td class='celdas_tabla'><p class='contact'>"+facturas[i].consecutivo+"</p></td><td class='celdas_tabla'><p class='contact'>"+facturas[i].fecha+"</p></td><td class='celdas_tabla tirar-derecha'><p class='contact'>"+monto+"</p></td></tr>"; 
	}
	$("#tbody_facturas").html(cuerpo);
}

function filtrarFacturasPorCodigo(codigo){
	nombre = $("#nombre").val();
	//Si el nombre es vacio, salir
	if(nombre.trim()===''){buscarCedula(null);}
	//Si el codigo no es numerico, salir
	if(!isNumber(codigo)){buscarCedula(null);}
	//Si la cedula no es numerica, salir
	cedula = $("#cedula").val();
	if(!isNumber(cedula)){buscarCedula(null);}
	//Si no hay facturas, salir
	cantFacturas = $('#tabla_facturas tr').length;
	if(cantFacturas==1){buscarCedula(null);}
	
	//Ejecutar
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/notas/getFacturasFiltradasCodigo',
		type: "POST",		
		//async: false,
		data: {'cedula':cedula, 'codigo':codigo},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				//alert(data.trim());
				informacion = $.parseJSON('[' + data.trim() + ']');
				//alert(JSON.stringify(informacion[0], null, 4));
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					//alert(JSON.stringify(informacion, null, 4));
					setFacturas(informacion[0].facturas);
				}
			}catch(e){
				//alert(e);
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
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

function setUpLiveSearch(){	
	$("#nombre").autocomplete({
		  source: location.protocol+'//'+document.domain+'/facturas/nueva/getNombresClientesBusqueda',
		  minLength: 1,
		  select: function( event, ui ) {
			$("#cedula").val(ui.item.id);
			evt = document.createEvent("KeyboardEvent");
			buscarCedula(evt); 		  
		  }
		});
}