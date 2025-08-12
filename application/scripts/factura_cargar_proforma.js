var existeProforma = true;

function setProforma(consecutivo){
	if(seCambioFactura){ //Si al cargarse de proforma se viene de una factura que fue editada debe valorarlo
		deshacerFacturaCaja();		
	}
	cargarEncabezadoProforma(consecutivo);
	if(existeProforma){
		cargarProductosProforma(consecutivo);
		consecutivoActual = consecutivo; //Asignamos el consecutivo actual para realizar operaciones
		isProforma = true;
	}
	$("#imagen_Cargar_proforma").hide();
	seCambioFactura = false; //Decimos que no se cambio fatcura
	window.onbeforeunload=null; //Eliminamos los eventos de salida
	window.onunload=null; //Eliminamos los eventos de salida	 
	disableInputs(); //Deshabilitamos las entradas

	//cerrarSelector();	
}

function cargarEncabezadoProforma(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/facturas/caja/getProformaHeaders",
		type: "POST",
		async: false,
		data: {'consecutivo':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaHEAD = $.parseJSON('[' + data.trim() + ']');
				if(facturaHEAD[0].status==="error"){
					displayErrors(facturaHEAD[0].error);
					existeProforma = false;
					isProforma = false;
					//Limpiamos la factura
					$("#contenidoArticulos").html('');
					resetCostosTotales();
					$("#cedula").val('');
					$("#nombre").val('');
					$("#observaciones").val('');
					updateProductsTotal();
					
				}else if(facturaHEAD[0].status==="success"){
					//alert("bien");
					
					setEncabezadoFactura(facturaHEAD);
					existeProforma = true;
				}
			}
			catch(e){
				//notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
				//window.location = location.protocol+'//'+document.domain+'/facturas/caja';
				//if(seCambioFactura){deshacerFacturaCaja();}
				//location.reload();
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{notyError('¡Hubo un error al cargar la proforma!');}
	});	
}

function cargarProductosProforma(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/facturas/caja/getArticulosProforma",
		type: "POST",
		data: {'consecutivo':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaBODY = $.parseJSON('[' + data.trim() + ']');
				if(facturaBODY[0].status==="error"){
					displayErrors(facturaBODY[0].error);
				}else if(facturaBODY[0].status==="success"){
					setProductosFactura(facturaBODY[0].productos);
				}
			}
			catch(e){
				//notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
				//window.location = location.protocol+'//'+document.domain+'/facturas/caja';
				//if(seCambioFactura){deshacerFacturaCaja();}
				//location.reload();
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{notyError('¡Hubo un error al cargar la proforma!');}
	});
}