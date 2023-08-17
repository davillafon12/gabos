$(function(){
	$("#cant_dec").numeric();
	$("#compra_dolar").numeric();
	$("#venta_dolar").numeric();
	$("#compra_min").numeric();
	$("#compra_inter").numeric();
	$("#cant_ses").numeric();
	$("#iva_cant").numeric();
	$("#iva_ret").numeric();
});

function actualizarEstadoClientes(){
	$.prompt("¡Esto actualizará el estado de cada cliente!", {
					title: "¿Esta seguro que desea realizar esta operación?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){													
													procesarActualizacion();
												}
											}
				});
}

function procesarActualizacion(){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/config/actualizarEstadoClientes',
		type: "POST",		
		//async: false,
		data: {},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){					
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					notyMsg('¡Se han actualizado los estados de los clientes con éxito!', 'success');
					$("#fecha_actualizacion_estado").html(informacion[0].fecha);
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyMsg('¡No se pudo realizar la actualización, contacte al administrador!', 'error');
		break;
		case '2':
			notyMsg('¡Usted no tiene permiso para realziar esta operación, contacte al administrador!', 'error');
		break;
		case '3':
			notyMsg('¡Debe haber pasado mínimo un mes desde la última actualización!', 'error');
		break;
		case '4':
			notyMsg('¡No se pudo cargar los clientes, contacte al administrador!', 'error');
		break;
		case '5':
			notyMsg('¡URL no válida, contacte al administrador!', 'error');
		break;
		case '6':
			notyMsg('¡Alguno de los datos tiene un formato no válido!', 'error');
		break;
	}
}

function actualizarServidorImpresion(){
	if(validarVariablesServidor()){
		$.prompt("¡Esto actualizará la información del servidor de impresión!", {
					title: "¿Esta seguro que desea realizar esta operación?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){													
													actualizarImpresion();
												}
											}
				});
	}
}

function validarVariablesServidor(){
	ip = $("#ip_servidor_impresion").val();
	puerto = $("#puerto_servidor_impresion").val();
	protocolo = $("#protocolo_servidor_impresion").val();
	
	//Se quito pues puede ser ip o dominio
	/*pattern = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/g;
    if(!pattern.test(ip)){
		notyMsg('¡Número de IP no válido!', 'error');
		return false;
	}*/
	
	if(!isNumber(puerto)){
		notyMsg('¡Número de puerto no válido!', 'error');
		return false;
	}
	
	if(protocolo.trim()!='http'&&protocolo.trim()!='https'){
		notyMsg('¡Protocolo no válido!', 'error');
		return false;
	}
	
	return true;
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function actualizarImpresion(){
	ip = $("#ip_servidor_impresion").val();
	puerto = $("#puerto_servidor_impresion").val();
	protocolo = $("#protocolo_servidor_impresion").val();
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/config/actualizarServidorImpresion',
		type: "POST",	
		data: {'ip':ip.trim(),'puerto':puerto.trim(),'protocolo':protocolo.trim()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){					
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					notyMsg('¡Se ha actualizado la información del servidor de impresión con éxito!', 'success');					
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}