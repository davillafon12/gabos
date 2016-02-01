var existeFacturaAplicar = false; //Se usa para verificar si la factura a la que se le aplica la nota existe o no

function validarFacturaAplicar(consecutivo){
	//Validaciones
	//Si es vacio
	if(consecutivo.trim()===''){existeFacturaAplicar = false; return false;}
	//Si la cedula no es numerica, salir
	cedula = $("#cedula").val();
	if(!isNumber(cedula)){
		manejarErrores('8');
		return false;
	}
	//Si no hay factura seleccionada, salir	
	if(!facturaSeleccionada){
		manejarErrores('9');
		return false;
	}
	//Si factura aplicar no es numerica
	if(!isNumber(consecutivo)){
		manejarErrores('7');
		return false;
	}
	
	//Ejecutar
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/contabilidad/notas/consecutivoFacturaExiste',
		type: "POST",		
		//async: false,
		data: {'cedula':cedula, 'consecutivo':consecutivo},				
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
					//setFacturas(informacion[0].facturas);
					//$("#tbody_productos").html('');
					//$("#tbody_productos_seleccionados").html('');
					//facturaSeleccionada = 0;
					existeFacturaAplicar = true;
					notyMsg('¡Factura válida para aplicar la nota crédito!', 'success');
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