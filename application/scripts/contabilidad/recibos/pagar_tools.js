function pagar(){
	if(validarEnvio()){
		$.prompt("¡Esto descontará saldo del cliente!", {
					title: "¿Esta seguro que desea saldar a este cliente?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){
													cedula = $("#cedula").val();
													saldoEscrito = $("#saldo_a_pagar_input").val();
													saldoEscrito = saldoEscrito.replace('.','');
													saldoEscrito = saldoEscrito.replace(',','.');
													enviarCobro(cedula, saldoEscrito);
												}
											}
				});	
	}
}

function validarEnvio(){
	//Obtenemos las variables
	cedula = $("#cedula").val();
	nombre = $("#nombre").val();
	cantidadFacturasCobrar = facturasSaldar.length;
	saldoEscrito = $("#saldo_a_pagar_input").val();
	saldoEscrito = saldoEscrito.replace('.','');
	saldoEscrito = saldoEscrito.replace(',','.');
	
	if(!isNumber(cedula)){
		notyMsg('¡Cédula no válida!', 'error');
		return false;
	}
	
	if(nombre.trim()===''){
		notyMsg('¡No ha digitado un nombre!', 'error');
		return false;
	}
	
	if(cantidadFacturasCobrar<=0){ //No hay facturas a saldar
		notyMsg('¡No hay facturas que saldar!', 'error');
		return false;
	}
	
	if(!isNumber(saldoEscrito)){
		notyMsg('¡Debe digitar un saldo válido!', 'error');
		return false;
	}else if(saldoEscrito<=0){ //Si el saldo es menor o es cero
		notyMsg('¡Debe digitar un saldo válido!', 'error');
		return false;
	}
	
	if(!validarPago()){
		return false;
	}
	
	return true;
}

function enviarCobro(cedula, saldo){
	

	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/recibos/saldarFacturas',
		type: "POST",		
		//async: false,
		data: {'cedula':cedula, 'saldoAPagar':saldo, 'facturas':JSON.stringify(facturasSaldar), 'tipoPago':JSON.stringify(tipoPagoJSON())},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				//alert(JSON.stringify(informacion[0], null, 4));
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					resetFields();
					buscarCedula(null); 
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function validarPago(){
	tipoPago = $('input[name=tipo]:checked').val();
	if(tipoPago.trim()==='tarjeta'){
		if(!validarTarjeta()){
			notyMsg('¡Debe ingresar un número de autorización válido!','error');
			return false;
		}
	}
	return true;
}

function validarTarjeta(){
	numeroTransaccion = $('#numero_transaccion').val();
	return $.isNumeric(numeroTransaccion);
}

function tipoPagoJSON(){
	tipoPago = $('input[name=tipo]:checked').val();
	if(tipoPago.trim()==='tarjeta'){
		return [{'tipo':'tarjeta','transaccion':$('#numero_transaccion').val(),'banco':$('#banco_sel').val()}];
	}else if(tipoPago.trim()==='contado'){
		return [{'tipo':'contado'}];
	}
}
