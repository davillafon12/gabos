/////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// ENVIAR A CAJA ////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

var invoiceItemsJSON = [];
var datosFactura = [];

function toCajaSubmit(){
	r = confirm("¿Esta seguro que desea envia esta factura a caja?");
	if (r == true) {
		doSubmit();
	} else {}	
}

function doSubmit(){
	createJSON();
	if(validarCampos()){
		//alert(JSON.stringify(invoiceItemsJSON, null, 4));
		//data = [];
		//data.push(getFullData());
		url = "/facturas/nueva/crearPendiente";
		//data.push(invoiceItemsJSON);
		//alert(JSON.stringify(data));
		//alert(url);
		$('#envio_factura').bPopup({
				modalClose: false
			});
		
		//Enviamos la factura
		sendInvoice(url);		
	}
	else
	{return false;}
}

function validarCampos(){
	cedula_field = document.getElementById("cedula").value;
	nombre_field = document.getElementById("nombre").value;
	if(cedula_field.trim()===''){
		n = noty({
					   layout: 'topRight',
					   text: '¡Falta llenar el campo de cédula!',
					   type: 'error',
					   timeout: 4000
					});
		return false;
	}
	if(nombre_field.trim()===''){
		n = noty({
					   layout: 'topRight',
					   text: '¡Falta llenar el campo de nombre del cliente!',
					   type: 'error',
					   timeout: 4000
					});
		return false;
	}
	tamJSONArray = invoiceItemsJSON.length;
	if(tamJSONArray<1){
		n = noty({
					   layout: 'topRight',
					   text: '¡No hay articulos en la factura!',
					   type: 'error',
					   timeout: 4000
					});
		return false;
	}
	if(clienteCanBuy==false){
		n = noty({
					   layout: 'topRight',
					   text: '¡Este cliente no puede comprar!',
					   type: 'error',
					   timeout: 4000
					});
		return false;
	}
	return true;
}

function createJSON(){
	invoiceItemsJSON=[]; //Limpiamos el array
	lengthArray = getTamañoIndexArray();
	for (var i = 0; i < lengthArray; i++) {
		index = array_pos_rows[i]; //Obtenemos el index
		j_ob = parseRowToJSON(index);
		if(j_ob){invoiceItemsJSON.push(j_ob);}//Se verifica que sea un item real de la factura
	}
}

function getTamañoIndexArray(){
	return array_pos_rows.length;
}

function parseRowToJSON(numRow){
	codigo = document.getElementById("codigo_articulo_"+numRow).value;
	descripcion = document.getElementById("descripcion_articulo_"+numRow).innerHTML;
	
	if(descripcion.trim()===''){ //Si solo esta el codigo pero no hay descripcion, osea articulo no cargado
		return false;
	}
	else{
		cantidad = document.getElementById("cantidad_articulo_"+numRow).value;
		descuento = document.getElementById("descuento_articulo_"+numRow).innerHTML;
	}
	
	precio_unitario = ''; //Por defecto es vacio
	
	if(codigo.trim()==='00'){ //Si es generico traer los demas datos necesarios
		precio_unitario = document.getElementById("costo_unidad_articulo_ORIGINAL_"+numRow).value;
	}
	else{
		descripcion = ''; //Si no es generico limpiamos descripcion para que el post no sea tan pesado
	}
	
	exento = document.getElementById("producto_exento_"+numRow).value;
	
	JSONRow = {co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento};
	
	return JSONRow;
	
}

function getFullData(){
	datosFactura=[];
	cedula_field = document.getElementById("cedula").value;
	nombre_field = document.getElementById("nombre").value;
	tipo_moneda = document.getElementById("tipo_moneda").value;
	observaciones = document.getElementById("observaciones").value;
	//limpiamos las observacion de escapeo
	observaciones = observaciones.replace("&","");
	observaciones = observaciones.replace(";","");
	observaciones = observaciones.replace("+","");
	
	return [{ce:cedula_field, no:nombre_field, cu:tipo_moneda, ob:observaciones}];
	
	//return '/facturas/nueva/crearPendiente?cedula='+cedula_field+'&nombre='+nombre_field+'&currency='+tipo_moneda+'&observaciones='+observaciones+'&invoiceItems='+JSON.stringify(invoiceItemsJSON);
}

function sendInvoice(URL){	
	$.ajax({
		url : location.protocol+'//'+document.domain+URL,
		type: "POST",
		data: {'head':JSON.stringify(getFullData()), 'items':JSON.stringify(invoiceItemsJSON)},		
		success: function(data, textStatus, jqXHR)
		{
			if(data.trim()==='7'){//Todo salio bien
				window.onbeforeunload=null;
				window.onunload=null;
				window.location = location.protocol+'//'+document.domain+'/home';
			}else{
				n = noty({
					   layout: 'topRight',
					   text: '¡Hubo un error en el envio de la factura. #'+data.trim(),
					   type: 'error',
					   timeout: 4000
					});
				$('#envio_factura').bPopup().close(); 
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
	 
		}
	});
}