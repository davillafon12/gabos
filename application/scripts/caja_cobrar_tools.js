var consecutivoActual = 0; //Alamacena el consecutivo de la factura cargada
var seCambioFactura = false; //EN el momento que se le da editar a una factura esto apsa a true
var isProforma = false; //Para ver si lo que se cargo es una proforma
var actualizarYCobrar = true; //Esto es para cobrar o no despues de actualizar una factura
isCajaLoaded = true; //Le decimos que esta en caja para validar si cliente tiene descuento y limpiar campos

function cobrarFactura(){
	//stopEnterProgVuelto = false;
	if(isProforma){
		//Crear factura y luego cobrarla
		/*if(crearFacturaFromProforma('/facturas/caja/creaFacturaFromProforma')){
			alert("");
		}*/
		if(validarFactura()){ //Validacion de campos de la factura
			if(validarPago()){ //Validacion del metodo de pago
				$.prompt("¡Esto imprimirá la factura!", {
					title: "¿Esta seguro que desea realizar el cobro de esta factura?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){
													$('#pop_up_pago_vuelto').bPopup({
														modalClose: false
													});
													$("#pop_cantidad_a_pagar").select();
													//stopEnterProgVuelto = true;
													//crearFacturaFromProforma('/facturas/caja/creaFacturaFromProforma');
												}
											}
				});
			}
		}
		//return false;
	}else{
		if(validarFactura()){ //Validacion de campos de la factura
			if(validarPago()){ //Validacion del metodo de pago
				$.prompt("¡Esto imprimirá la factura!", {
					title: "¿Esta seguro que desea realizar el cobro de esta factura?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){
													$('#pop_up_pago_vuelto').bPopup({
														modalClose: false
													});
													$("#pop_cantidad_a_pagar").select();
													totalAPagar = $("#costo_total").val();	
		
													tipoPago = $('input[name=tipo]:checked').val();	
													//alert(totalAPagar);
													
													$("#cuadro_vuelto_total").html(totalAPagar);
													if(tipoPago.trim()==='tarjeta'||tipoPago.trim()==='cheque'||tipoPago.trim()==='deposito'){
														totalAPagar = '0';
													}
													$("#vueltoDar").html('-'+totalAPagar);
													
												}
											}
				});
			}
		}
	}
}

function cerrarVuelto(){
	$('#pop_up_pago_vuelto').bPopup().close();
}

var stopEnterProgVuelto = false; //Para evitar que un evento de enter se siga propagando

function moverAceptarBoton(e, value){
	if (e.keyCode == 13) //Enter
	{
		//alert(stopEnterProgVuelto);
		if(stopEnterProgVuelto){document.getElementById("boton_aceptar_popup_vuelto").focus();}
		else{stopEnterProgVuelto = true;}	
	}	
	cantidadFloat = parseFloat(value);
	if(isNumber(cantidadFloat)){
		decimales = $("#cantidad_decimales").val();
		decimales_int = parseInt(decimales);
	
		totalAPagar = $("#costo_total").val();	
		
		tipoPago = $('input[name=tipo]:checked').val();
		if(tipoPago.trim()==='mixto'){
			totalAPagar = $('#monto_efectivo_mixto_input').val();
		}
		if(tipoPago.trim()==='tarjeta'||tipoPago.trim()==='cheque'||tipoPago.trim()==='deposito'){
			totalAPagar = '0';
		}
		
		//total_pagar_vuelto = parseFloat(totalAPagar);
		//total_pagar_vuelto = total_pagar_vuelto.toFixed(decimales_int);
		//total_pagar_vuelto = parseFloat(totalAPagar);
		//total_pagar_vuelto = total_pagar_vuelto.format(2, 3, '.', ',');
		//$("#cuadro_vuelto_total").html(total_pagar_vuelto);
		
		totalAPagar = totalAPagar.replace(',',''); //Limpiamos el formato
		totalAPagarFloat = parseFloat(totalAPagar);
		valorFinal = cantidadFloat-totalAPagarFloat;
		//if(valorFinal<0){$("#vueltoDar").html('Cantidad menor');}
		//else{
			
			valorFinal = valorFinal.toFixed(decimales_int);
			valorFinal = parseFloat(valorFinal);
			valorFinal = valorFinal.format(2, 3, '.', ',');
		
		
			//tipoMonedaVuelto = $("#tipo_moneda").val();
			//if(tipoMonedaVuelto==="colones"){tipoMonedaVuelto="₡";}else{tipoMonedaVuelto="$"}
			//$("#vueltoDar").html(tipoMonedaVuelto+valorFinal);
			$("#vueltoDar").html(valorFinal);
		//}
		
	}
}

function cobrarEImprimirPostPopUp(){
	cerrarVuelto();
	if(isProforma){
		crearFacturaFromProforma('/facturas/caja/creaFacturaFromProforma');
	}else{
		$('#envio_factura').bPopup({
			modalClose: false
		});
		window.onbeforeunload=null;
		window.onunload=null;
		if(seCambioFactura){
			cambiarFactura('/facturas/caja/cambiarFactura');
		}else{
			enviarCobro('/facturas/caja/cobrarFactura');
		}
	}
}

function validarFactura(){
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
	if(nombre_field.trim()===''||nombre_field.trim()==='No existe cliente!!!'){
		n = noty({
					   layout: 'topRight',
					   text: '¡Falta llenar el campo de nombre del cliente!',
					   type: 'error',
					   timeout: 4000
					});
		return false;
	}
	productosCantidad = document.getElementById("tabla_productos").rows.length-1;
	//Verifica si hay productos por cantidad de filas de la tabla
	if(productosCantidad<1){
		n = noty({
					   layout: 'topRight',
					   text: '¡No hay articulos en la factura!',
					   type: 'error',
					   timeout: 4000
					});
		return false;
	}
	//Verifica si hay productos ingresados
	createJSON();
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
	return true;
}

function validarPago(){
	tipoPago = $('input[name=tipo]:checked').val();
	if(tipoPago.trim()==='tarjeta'){
		if(!validarTarjeta()){
			alertaPago('¡Debe ingresar un número de autorización válido!','#numero_transaccion');
			return false;
		}
	}else if(tipoPago.trim()==='deposito'){
		if(!validarDeposito()){
			alertaPago('¡Debe ingresar un número de deposito válido!','#numero_deposito');
			return false;
		}
	}else if(tipoPago.trim()==='cheque'){
		if(!validarCheque()){
			alertaPago('¡Debe ingresar un número de cheque válido!','#numero_cheque');
			return false;
		}
	}else if(tipoPago.trim()==='mixto'){
		if(!validarMixto()){
			alertaPago('¡Debe ingresar un número de autorización, monto de tarjeta y/o monto en efectivo válido!','#numero_transaccion');
			return false;
		}
	}else if(tipoPago.trim()==='apartado'){
		if(!validarApartado()){
			alertaPago('¡Debe ingresar monto de abono válido!','#cantidad_abono');
			return false;
		}
	}
	return true;
}

function validarTarjeta(){
	numeroTransaccion = $('#numero_transaccion').val();
	if(numeroTransaccion.trim() == ''){return false;}
	return true;
}

function alertaPago(Msj, inputToBlink){
	n = noty({
		   layout: 'topRight',
		   text: Msj,
		   type: 'error',
		   timeout: 4000
		});
	$("#numero_transaccion").fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
}

function validarDeposito(){
	numeroTransaccion = $('#numero_deposito').val();
	return $.isNumeric(numeroTransaccion);
}

function validarCheque(){
	//numeroTransaccion = $('#numero_cheque').val();
	//return $.isNumeric(numeroTransaccion);
	return true;
}

function validarMixto(){
	numeroTransaccion = $('#numero_transaccion').val();
	transacIsNumber = $.isNumeric(numeroTransaccion);
	cantidadTransaccion = $('#cantidad_mixto').val();
	cantidadIsNumber = $.isNumeric(cantidadTransaccion);
	cantidadTransaccionEfectivo = $('#monto_efectivo_mixto_input').val();
	cantidadIsNumberEfectivo = $.isNumeric(cantidadTransaccionEfectivo);
	if(transacIsNumber&&cantidadIsNumber&&cantidadIsNumberEfectivo){
		cantidadTotalFactura = $("#costo_total").val();
		//Se hace varias veces por si es millones o mas
		cantidadTotalFactura = cantidadTotalFactura.replace(',','');
		cantidadTotalFactura = cantidadTotalFactura.replace(',','');
		cantidadTotalFactura = cantidadTotalFactura.replace(',','');
		cantidadTotalFactura = parseFloat(cantidadTotalFactura);
		//alert(parseFloat(cantidadTransaccion)+parseFloat(cantidadTransaccionEfectivo));
		if((parseFloat(cantidadTransaccion)+parseFloat(cantidadTransaccionEfectivo))===cantidadTotalFactura){
			return true;
		}else{
			return false;
		}
	}
	return false;
}

function validarApartado(){
	abono = $("#cantidad_abono").val();
	if($.isNumeric(abono)){
		cantidadTotalFactura = $("#costo_total").val();
		cantidadTotalFactura = cantidadTotalFactura.replace(',','');
		cantidadTotalFactura = parseFloat(cantidadTotalFactura);
		abono = parseFloat(abono);
		cantidadTotalFactura -= abono;
		if(cantidadTotalFactura>=0){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function tipoPagoJSON(){
	tipoPago = $('input[name=tipo]:checked').val();
	if(tipoPago.trim()==='tarjeta'){
		return [{'tipo':'tarjeta','transaccion':$('#numero_transaccion').val(),'banco':$('#banco_sel').val()}];
	}else if(tipoPago.trim()==='deposito'){
		return [{'tipo':'deposito','deposito':$('#numero_deposito').val(),'banco':$('#banco_sel').val()}];
	}else if(tipoPago.trim()==='cheque'){
		return [{'tipo':'cheque','cheque':$('#numero_cheque').val(),'banco':$('#banco_sel').val()}];
	}else if(tipoPago.trim()==='mixto'){
		return [{'tipo':'mixto','transaccion':$('#numero_transaccion').val(),'cantidad':$('#cantidad_mixto').val(),'banco':$('#banco_sel').val()}];
	}else if(tipoPago.trim()==='credito'){
		return [{'tipo':'credito','canDias':$('#cant_dias_credito').val()}];
	}else if(tipoPago.trim()==='apartado'){
		return [{'tipo':'apartado','abono':$('#cantidad_abono').val()}];
	}else if(tipoPago.trim()==='contado'){
		return [{'tipo':'contado'}];
	}
}

function enviarCobro(URL){
	$.ajax({
		url : location.protocol+'//'+document.domain+URL,
		type: "POST",
		data: {'consecutivo':consecutivoActual,'tipoPago':JSON.stringify(tipoPagoJSON())},		
		success: function(data, textStatus, jqXHR)
		{
			
			try{
				facturaHEAD = $.parseJSON('[' + data.trim() + ']');
			if(facturaHEAD[0].status==="error"){
				displayErrors(facturaHEAD[0].error);
				$('#envio_factura').bPopup().close();
			}else if(facturaHEAD[0].status==="success"){
				$('#envio_factura').bPopup().close();	
				if(tipoImpresion==='t'){
					//Impresion termica
					window.open(facturaHEAD[0].servidor_impresion+'/index.html?t='+facturaHEAD[0].token+'&d=f&n='+consecutivoActual+'&s='+facturaHEAD[0].sucursal+'&i='+tipoImpresion+'&server='+document.domain+'&protocol='+location.protocol,'Impresion de Factura','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
				}else if(tipoImpresion==='c'){
					//Impresion carta
					window.open(location.protocol+'//'+document.domain+'/impresion?t='+facturaHEAD[0].token+'&d=f&n='+consecutivoActual+'&s='+facturaHEAD[0].sucursal+'&i='+tipoImpresion,'Impresion de Factura','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
				}
				window.location = location.protocol+'//'+document.domain+'/facturas/caja';
			}
			}
			catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
	 
		}
	});
}


function cambiarFactura(URL){
	createJSON();
	
	$.ajax({
		url : location.protocol+'//'+document.domain+URL,
		type: "POST",		
		async: false,
		data: {'consecutivo':consecutivoActual,'head':JSON.stringify(getFullData()), 'items':JSON.stringify(invoiceItemsJSON), 'token':token_factura_temporal},				
		success: function(data, textStatus, jqXHR)
		{
				try{
						facturaHEAD = $.parseJSON('[' + data.trim() + ']');
						if(facturaHEAD[0].status==="error"){
								displayErrors(facturaHEAD[0].error);
								return false;
						}else if(facturaHEAD[0].status==="success"){
								if(actualizarYCobrar){ //Si se actualiza al cobrar tons que lo haga, puede ser que se actualice pero no se cobre
										enviarCobro('/facturas/caja/cobrarFactura');
								}else{
										n = noty({
										   layout: 'topRight',
										   text: 'Se ha actualizado la factura con éxito',
										   type: 'success',
										   timeout: 4000
										});
										actualizarYCobrar = true;
								}
						}
				}
				catch(e){
					notyError('¡No se pudo actualizar la factura, contacte al administrador!');
				}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

/////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////// ANULAR FACTURA /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////


function anularFactura(){	
	if(isProforma){
		return false;
	}
	if(validarFactura()){
		$('#pop_up_administrador').bPopup({
			modalClose: false
		});
		document.getElementById("pop_usuario").select();
		numeroPopUp='3';	
	}	
}

function closeAdministrador(){
	$('#pop_up_administrador').bPopup().close(); 
}

function anularPost(){
	$.prompt("¡Esto anulará la factura!", {
				title: "¿Esta seguro que desea anular esta factura?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){
												$('#pop_up_administrador').bPopup().close(); 
												//Nos aseguramos que estos eventos nunca se van a ejecutar
												window.onbeforeunload=null;
												window.onunload=null;
												//Devolvemos inventario si se edito
												if(seCambioFactura){deshacerFacturaCaja();}
												//Anulamos la factura
												anularFacturaAJAX("/facturas/caja/anularFactura");												
											}
										}
			});
}

function anularFacturaAJAX(URL){
	$.ajax({
		url : location.protocol+'//'+document.domain+URL,
		type: "POST",
		data: {'consecutivo':consecutivoActual},		
		success: function(data, textStatus, jqXHR)
		{
			try{
				result = $.parseJSON('[' + data.trim() + ']');
				if(result[0].status==="error"){
					displayErrors(result[0].error);
				}else if(result[0].status==="success"){
					window.location = location.protocol+'//'+document.domain+'/facturas/caja';
				}
			}catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
	 
		}
	});
}

function crearFacturaFromProforma(URL){
	$.ajax({
		url : location.protocol+'//'+document.domain+URL,
		type: "POST",
		async: false,
		data: {'consecutivo':consecutivoActual},		
		success: function(data, textStatus, jqXHR)
		{
			try{
				result = $.parseJSON('[' + data.trim() + ']');
				if(result[0].status==="error"){
					displayErrors(result[0].error);
					return false;
				}else if(result[0].status==="success"){
					//window.location = location.protocol+'//'+document.domain+'/facturas/caja';
					consecutivoActual = result[0].consecutivo; //Asignamos el consecutivo de la factura	
					//isProforma=false;
					
					//Cobramos la factura
					$('#envio_factura').bPopup({
						modalClose: false
					});
					window.onbeforeunload=null;
					window.onunload=null;					
					enviarCobro('/facturas/caja/cobrarFactura');
					//cobrarFactura();
				}
			}catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
	 
		}
	});
}

/////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////// EDITAR FACTURA /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

function editarFactura(){	
	if(isProforma){
		return false;
	}
	if(validarFactura()){
		$('#pop_up_administrador').bPopup({
			modalClose: false
		});
		document.getElementById("pop_usuario").select();
		numeroPopUp='4';	
	}	
}

function makeFacturaEditable(){  // FALTA VALIDAR seCambioFactura en dos metodos
	if(consecutivoActual===0){return false;} //Si no se ha ingresado una factura
	else{
		seCambioFactura = true;
		$('#cedula').attr("disabled", false);
		$('#nombre').attr("disabled", false);
		$('#tipo_moneda').attr("disabled", false);
		enableArticulosInputs();
		enableArticulosCantidades();
		enableArticulosArrows();
		$("#observaciones").attr("disabled", false);
		window.onbeforeunload=advertenciaSalida;
		window.onunload=deshacerFacturaCaja;
		
		//Agregar boton de guardar
		$("#boton_guardar_editar").css('display','inline-block');
		/*$.prompt("¡Esto habilitará los cambios en la factura!", {
				title: "¿Esta seguro que desea editar esta factura?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											console.log("ENTRO");
											if(v){												
												seCambioFactura = true;
												$('#cedula').attr("disabled", false);
												$('#nombre').attr("disabled", false);
												$('#tipo_moneda').attr("disabled", false);
												enableArticulosInputs();
												enableArticulosCantidades();
												enableArticulosArrows();
												$("#observaciones").attr("disabled", false);
												window.onbeforeunload=advertenciaSalida;
												window.onunload=deshacerFacturaCaja;
												
												//Agregar boton de guardar
												$("#boton_guardar_editar").css('display','inline-block');
												
												
												
											}
											$.prompt.close();
										}
			});*/
	}	
}

function actualizarFactura(){
				if(validarFactura()){
						if(seCambioFactura){
								actualizarYCobrar = false;
								cambiarFactura('/facturas/caja/cambiarFactura');
						}else{
								notyError('¡La edición de la factura debe estar habilitada!');
						}																																																																																																																														
				}	
}

function enableArticulosCantidades()
{
	inputsCodigo = document.getElementsByClassName('cantidad_articulo');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].disabled=false;
	}
}

function enableArticulosArrows()
{
	inputsCodigo = document.getElementsByClassName('imagen_arrow');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].style.display='block';
	}
}

function deshacerFacturaCaja(){
	
	createJSON();
	
	$.ajax({
		url : location.protocol+'//'+document.domain+"/facturas/caja/devolverInventario",
		type: "POST",
		async: false,
		data: {'consecutivo':consecutivoActual,'items':JSON.stringify(invoiceItemsJSON)},		
	});
	//Que el programa haga lo que el usuario decidio
}


function salidaSesion(){
	if(seCambioFactura){deshacerFacturaCaja();}
	window.onbeforeunload=null;
	window.location = location.protocol+'//'+document.domain+'/home/logout';
}

// Para el tamaño del windows open
var anchoImpresion = 290;
var alturaImpresion = 400;

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
