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
		url : location.protocol+'//'+document.domain+'/config/actualizarEstadoClientes',
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
	}
}
