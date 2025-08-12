$( document ).ready(function() {
    $("#recibo").numeric();
	$("#deposito").numeric();
});

function crearDeposito(){
	if(validar()){
		$.prompt("¡Agregar Confirmación de Deposito por Recibo de Dinero!", {
					title: "¿Esta seguro que desea confirmar este depósito?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){													
													recibo = $("#recibo").val();
													deposito = $("#deposito").val();
													banco = $("#banco").val();
													envioDeposito(recibo, deposito, banco);
												}
											}
				});		
	}
}

function validar(){
	recibo = $("#recibo").val();
	deposito = $("#deposito").val();
	if(!isNumber(recibo)){
		notyMsg('Ingrese un número de recibo válido', 'error');
		return false;
	}
	if(!isNumber(deposito)){
		notyMsg('Ingrese un número de depósito válido', 'error');
		return false;
	}
	return true;
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function envioDeposito(recibo, deposito, banco){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/contabilidad/deposito/guardarDeposito',
		type: "POST",	
		data: {'recibo':recibo, 'deposito':deposito, 'banco':banco},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					notyMsg('¡Depósito de Recibo por Dinero ingresado con éxito!','success');
					resetFields();
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function resetFields(){
	$("#recibo").val('');
	$("#deposito").val('');
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyMsg('¡No se pudo tramitar la información, contacte al administrador!','error');
		break;
		case '2':
			notyMsg('¡Error en el envio de la URL, contacte al administrador!','error');
		break;
		case '3':
			notyMsg('¡Dato obligatorio vacío, contacte al administrador!','error');
		break;
		case '4':
			notyMsg('¡Recibo ingresado no existe!','error');
		break;
		case '5':
			notyMsg('¡Banco seleccionado no existe!','error');
		break;			
	}
}