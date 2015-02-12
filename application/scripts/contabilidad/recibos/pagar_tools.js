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
					notyMsg('¡Se ha creado el recibo con éxito!', 'success');
					resetFields();
					buscarCedula(null);
					if(tipoImpresion==='t'){
						//Impresion termica
						window.open(informacion[0].servidor_impresion+'/index.html?t='+informacion[0].token+'&d=r&n='+informacion[0].recibos.join()+'&s='+informacion[0].sucursal+'&i='+tipoImpresion+'&server='+document.domain+'&protocol='+location.protocol,'Impresion de Recibos','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
					}else if(tipoImpresion==='c'){
						//Impresion carta
					}
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
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

function validarPago(){
	tipoPago = $('input[name=tipo]:checked').val();
	if(tipoPago.trim()==='tarjeta'){
		if(!validarTarjeta()){
			notyMsg('¡Debe ingresar un número de autorización válido!','error');
			return false;
		}
	}else if(tipoPago.trim()==='deposito'){
		deposito = $('#numero_documento').val();
		if(deposito.trim()===''){
			notyMsg('¡Debe ingresar un número de documento válido!','error');
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
	}else if(tipoPago.trim()==='deposito'){
		return [{'tipo':'deposito', 'documento':$('#numero_documento').val()}];
	}else if(tipoPago.trim()==='contado'){
		return [{'tipo':'contado'}];
	}
}


